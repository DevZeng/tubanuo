<?php


namespace App\Http;


use App\Handler\AddressHandler;
use App\Handler\DeviceHandler;
use App\Handler\FaceHandler;
use App\Jobs\NotifySchool;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;
use Workerman\Timer;
define('HEARTBEAT_TIME', 120);
class WorkermanHandler
{
    protected $connections = [];
    // 处理客户端连接
    public function onConnect($connection)
    {
        echo "new connection from ip " . $connection->getRemoteIp() . "\n";
    }

    // 处理客户端消息
    public function onMessage($connection, $data)
    {
//        var_dump($data);
        $data = json_decode($data,true);
        var_dump($data);
//        dd($data);
        if (!$data){
            $message = [
                "code"=>false,
                "log"=>"empty message",
            ];
            return $connection->send(json_encode($message));
        }
        $message = [
            "code"=>true,
            "log"=>"'".$data['request_type']."'"."success!"
        ];
        if (isset($data['device_id'])&&!isset($connection->device_id)){
            $connection->device_id = $data['device_id'];
//            setDeviceTime($data['device_id'],time());
        }

        print $connection->device_id.'返回消息';
                        print "\n";
//
//                        $command = $connection->commands[0];
//                        $data = json_decode($command->data);
//                        $data->request_id = $command->id;
                        print json_encode($message);
        $connection->send(json_encode($message));
        return ;

        $connection->lastMessageTime = time();
        if (isset($data['request_id'])){
            if ($data['code']==="true"){
                DeviceHandler::createDeviceCommand($data['request_id'],[
                    'state'=>3
                ]);
                $command = isset($connection->commands[0])?$connection->commands[0]:null;
                if (isset($command)&&$command!=null&&$command->id==$data['request_id']){
                    switch ($command->command){
                        case 'getUserList':
                            setRedisData('userList_'.$command->device_uuid,serialize(
                                $data['user_id_list']
                            ));
                            break;
                        case 'getSoftVersion':
                            DeviceHandler::setDeviceByUUID($command->device_uuid,[
                                'version'=>$data['version']
                            ]);
                            break;
                    }
                }
                unset($connection->commands[0]);
                sort($connection->commands);
            }else{
                $connection->commands[0]->retries = isset($connection->commands[0]->retries)?$connection->commands[0]->retries+1:1;
                if ($connection->commands[0]->retries==3){
                    DeviceHandler::createDeviceCommand($data['request_id'],[
                        'state'=>4,
                        'report'=>isset($data['log'])?$data['log']:''
                    ]);
                    unset($connection->commands[0]);
                    sort($connection->commands);
                }
            }
        }else{
            switch ($data['request_type']){
                case 'deviceOnline':
                    $device = DeviceHandler::getDeviceByUUID($data['device_id']);
                    if (!$device){
                        DeviceHandler::createUnknownDevice($data['device_id'],$connection->getRemoteIp());
                    }
                    $connection->device_id = $data['device_id'];
                    $message = [
                        "device_id"=>$data['device_id'],
                        "code"=>true,
                        "log"=>"'".$data['request_type']."'"."success!"
                    ];
                    $connection->send(json_encode($message));
                    break;
                case 'heartbeat':
                    DeviceHandler::createDeviceHeartBeat($data['device_id'],$data['timestamp']);
                    $message = [
                        "code"=>true,
                        "log"=>"'".$data['request_type']."'"."success!"
                    ];
                    $connection->send(json_encode($message));
                    break;
                case 'faceRecognition':
                    $device = DeviceHandler::getDeviceByUUID($data['device_id']);
                    $image = "data:image/jpg;base64,".$data['image'];
                    $base = str_replace('\/','/',$image);
                    $base = str_replace("\n",'',$base);
                    $decoder = new Base64ImageDecoder($base, ['jpeg', 'jpg', 'png', 'gif']);
                    $fileName = strtoupper(Str::uuid()) . "." . $decoder->getFormat();
                    $disk = Storage::disk('qiniu');
                    $disk->put($fileName,$decoder->getDecodedContent());
                    if ($device){
                        $address = AddressHandler::getAddress($device->address_id);
                        if(isset($data['user_id'])){
                            $face = FaceHandler::getFaceByFaceId($data['user_id']);
                            dispatch(new NotifySchool($face->number,$device->direction,($data['timestamp']-8*60*60),$disk->downloadUrl($fileName,'https'),$face->notify_url,$face->face_id));
                        }
                        FaceHandler::postFaceLog([
                            'project_id'=>$address->project_id,
                            'address_id'=>$address->id,
                            'device_uuid'=>$device->uuid,
                            'image'=>$disk->downloadUrl($fileName,'https'),
                            'timestamp'=>$data['timestamp'],
                            'face_id'=>isset($data['user_id'])?$data['user_id']:'stranger',
                            'face_token'=>'',
                            'temp'=>0,
                            'open_id'=>'',
                            'notify'=>5,
                            'face_type'=>'USER'
                        ]);
                    }else{
                        FaceHandler::postFaceLog([
                            'project_id'=>0,
                            'address_id'=>0,
                            'device_uuid'=>$data['device_id'],
                            'image'=>$disk->downloadUrl($fileName,'https'),
                            'timestamp'=>$data['timestamp'],
                            'face_id'=>isset($data['user_id'])?$data['user_id']:'stranger',
                            'face_token'=>'',
                            'temp'=>0,
                            'open_id'=>'',
                            'notify'=>5,
                            'face_type'=>'USER'
                        ]);
                    }
                    $message = [
                        "code"=>true,
                        "log"=>"'".$data['request_type']."'"."success!"
                    ];
                    $connection->send(json_encode($message));
                    break;
                default:
                    $this->checkDeviceId($connection);
                    break;
            }
        }

    }
    public function checkDeviceId($connection)
    {
        if (!isset($connection->device_id)){
            $message = [
                "code"=>false,
                "log"=>"need register",
            ];
            $connection->send(json_encode($message));
            $connection->close();
        }
    }

    // 处理客户端断开
    public function onClose($connection)
    {
        echo "connection closed from ip {$connection->getRemoteIp()}\n";
    }

    public function onWorkerStart($worker)
    {
        Timer::add(3, function () use ($worker) {
            $time_now = time();
//            var_dump($worker->connections);
            foreach ($worker->connections as $connection) {
//                if (isset($connection->device_id)){
//                    if (!isset($connection->commands)||count($connection->commands)==0){
//                        $connection->commands = DeviceHandler::getDeviceCommandsOrigin($connection->device_id,100);
//                    }else {
//                        print $connection->device_id.'下发指令';
//                        print "\n";
//
//                        $command = $connection->commands[0];
//                        $data = json_decode($command->data);
//                        $data->request_id = $command->id;
//                        print json_encode($data);
//                        $connection->send(json_encode($data));
//                    }
//                }
                // 有可能该connection还没收到过消息，则lastMessageTime设置为当前时间
                if (empty($connection->lastMessageTime)) {
                    $connection->lastMessageTime = $time_now;
                    continue;
                }
                // 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
                if ($time_now - $connection->lastMessageTime > HEARTBEAT_TIME) {
                    echo "Client ip {$connection->getRemoteIp()} timeout!!!\n";
                    $connection->close();
                }
            }
        });
    }
}