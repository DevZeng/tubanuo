<?php

namespace App\Http\Controllers;

use App\Libraries\Wxxcx;
use App\Models\SchoolNotify;
use App\Models\StudentStatus;
use APP\Modules\User\UserHandle;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{
    //
    public function Notice($stu_id){
        $student=Student::where('stu_id',$stu_id)->first();
        $template="sExfJnV0OMkzfcSNSNxpBbl6DrLO2VLXfIPCejmM1lM";
        $data=[
            'touser'=>$student->user_openid,
            'template_id'=>$template,
            'miniprogram'=>[
                'appid'=>""
            ],
            'data'=>[
                'first'=>[
                    'value'=>"您好,审核申请已经有结果了"
                ],
                'keyword1'=>[
                    'value'=>$student->stu_name
                ],
                'keyword2'=>[
                    'value'=>$student->stu_status==1?'通过':'不通过'
                ],
                'keyword3'=>[
                    'value'=>date('Y年m月d H:i:s')
                ],
                'remark'=>[
                    'value'=>$student->stu_status==1?"审核通过":"审核不通过,请重新提交"
                ]
            ],
        ];
        $access_token=getUserToken('access_token');
        if ($access_token){
            $url=sprintf('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s',$access_token);
            $wx=new Wxxcx('wxa45e3bb7239c5059','65c369313719a3e02d9b905f13d9981e');
            $redata = $wx->request($url,json_encode($data));
            dump($redata);
            if ($redata['errcode']==0){
                $stu=new StudentStatus();
                $stu->student_id=$student->stu_id;
                $stu->stu_status=$student->stu_status;
                $stu->save();
            }else{
                setRedisData('refresh',1);
            }
        }else{
            setRedisData('refresh',1);
        }

        return true;
    }
    public function getUser(Request $post){
        $openid=$post->user_openid;
        $user=DB::table('fb_user')->where('user_openid',$openid)->first();
        if (!$user){
            return jsonResponse([
                'msg'=>'error'
            ]);
        }
        $teach=DB::table('fb_teacher_apply')->where('user_openid',$openid)->orderBy('user_id','DESC')->first();
        if ($teach){
            $class=DB::table('fb_class')->where('class_id',$teach->class_id)->first();
            $user->class_grade=$class?$class->class_grade:'';
            $user->class_name=$class?$class->class_name:'';
            $user->work_number=$teach?$teach->work_number:'';
            $user->user_head1=$teach?$teach->user_head1:'';
            $user->subjects=$teach?$teach->subjects:'';
            $user->status=$teach?$teach->status:0;
            $user->apply_teacher = $teach?1:0;
            $user->apply_whether = $teach?$teach->whether:0;
        }
        $worker=DB::table('fb_sch_staff')->where('user_openid',$openid)->select('staff_id','positions','date1','staff_status',"user_images1")->orderBy('staff_id','DESC')->first();
        if ($worker){
            $user->positions=$worker->positions;
            $user->date1=$worker->date1;
            $user->user_images1=$worker->user_images1;
            $user->staff_status=$worker->staff_status;
            $user->apply_staff = $worker->staff_status==2?0:1;
        }else{
            $user->apply_staff = 0;
        }
        return response()->json([
            'msg'=>"ok",
            'user'=>$user
        ]);
    }

    public function getClass(){
        $class=DB::table('fb_class')->get();
        return response()->json([
            'msg'=>"ok",
            'data'=>$class
        ]);
    }

    public function updateUser(Request $post){
        $userid=$post->user_openid;
        $data=[
            'creat_time'=>date('Y-m-d H:i:s',time()),
            'teacher'=>$post->teacher,
            'user_address'=>$post->user_address,
            'user_age'=>$post->user_age,
            'user_alias'=>$post->user_alias,
            'user_card'=>$post->user_card,
            'user_id'=>$post->user_id,
            'user_image'=>$post->user_image,
            'user_iphone'=>$post->user_iphone,
            'user_name'=>$post->user_name,
            'user_openid'=>$post->user_openid,
            'user_sex'=>$post->user_sex,
            'whether'=>$post->whether,
        ];
        //dd(isset($post->staff_status));
        $teacher=DB::table('fb_teacher_apply')->where('user_openid',$post->user_openid)->first();
        //dd($teacher);
        $checkwork=DB::table('fb_sch_staff')->where('user_openid',$post->user_openid)->first();
        //dd($checkwork);
        if ($post->teacher == 1){
            //dd(321);
            if ($checkwork){
                DB::table('fb_sch_staff')->where('user_openid',$post->user_openid)->delete();
            }
            if ($teacher == null){
                $t_data=[
                    'user_openid'=>$post->user_openid,
                    'whether'=>$post->whether,
                    'user_head1'=>$post->user_head1,
                    'user_head2'=>1,
                    'class_id'=>$post->class_id,
                    'work_number'=>$post->work_number,
                    'subjects'=>$post->subjects,
                    'status'=>1,
                    'form_id'=>"xxx",
                    'creat_time'=>date('Y-m-d H:i:s',time()),
                ];

                DB::table('fb_teacher_apply')->insert($t_data);

            }else{
                $t_data=[
                    'user_openid'=>$post->user_openid,
                    'whether'=>$post->whether,
                    'user_head1'=>$post->user_head1,
                    'user_head2'=>1,
                    'class_id'=>$post->class_id,
                    'work_number'=>$post->work_number,
                    'subjects'=>$post->subjects,
                    'status'=>1,
                    'form_id'=>"xxx",
                    'creat_time'=>date('Y-m-d H:i:s',time()),
                ];
                //dd($t_data);
                DB::table('fb_teacher_apply')->where('user_openid',$post->user_openid)->update($t_data);
                //dd($res);
            }

        }else{
            if (isset($post->staff_status) == true){
                if ($teacher){
                    //dd(123);
                    DB::table('fb_teacher_apply')->where('user_openid',$post->user_openid)->delete();
                    DB::table('fb_user')->where('user_openid',$post->user_openid)->update([
                        'teacher'=>0,
                        'whether'=>0,

                    ]);
                }
                if ($checkwork !== null){
                    $w_data=[
                        'user_openid'=>$post->user_openid,
                        'positions'=>$post->positions,
                        'user_images1'=>$post->user_images1,
                        'user_images2'=>1,
                        'date1'=>$post->date1,
                        'staff_status'=>0,
                        'creatime'=>date('Y-m-d H:i:s',time())
                    ];

                    //dd($w_data);
                    DB::table('fb_sch_staff')->where('user_openid',$post->user_openid)->update($w_data);
                }else{
                    $w_data=[
                        'user_openid'=>$post->user_openid,
                        'positions'=>$post->positions,
                        'user_images1'=>$post->user_images1,
                        'user_images2'=>1,
                        'date1'=>$post->date1,
                        'staff_status'=>0,
                        'creatime'=>date('Y-m-d H:i:s',time())
                    ];
                    DB::table('fb_sch_staff')->insert($w_data);
                }
            }
        }
        DB::table('fb_user')->where('user_openid',$userid)->update($data);
        return response()->json([
            'msg'=>"ok"
        ]);
    }
    public function addUser(Request $post){
        $data=[
            'user_openid'=>$post->user_openid,
            'user_image'=>$post->user_image,
            'user_alias'=>$post->user_alias,
            'user_name'=>$post->user_name,
            'user_sex'=>$post->user_sex,
            'user_iphone'=>$post->user_iphone,
            'user_age'=>0,
            'user_card'=>$post->user_card,
            'user_address'=>$post->user_address,
            'creat_time'=>date('Y-m-d H:i:s',time())
        ];
        if ($post->staff_status){
            $w_data=[
                'user_openid'=>$post->user_openid,
                'positions'=>$post->positions,
                'user_images1'=>$post->user_images1,
                'date1'=>$post->date1,
                'staff_status'=>0,
                'creatime'=>date('Y-m-d H:i:s',time())
            ];
            DB::table('fb_sch_staff')->where('user_openid',$post->user_openid)->update($w_data);
        }
        $res=DB::table('fb_user')->insert($data);
        if ($res){
            return response()->json(
                [
                    'msg'=>"ok"
                ]
            );
        }

    }

    public function addTeacher(Request $post){
        /*$data=[
            "class_grade"=>$post->class_grade,
            'class_id'=>$post->class_id,
            'class_name'=>$post->class_name,
            'creat_time'=>date('Y-m-d H:i:s',time()),
            'department'=>$post->department,
            "form_id"=>"xxx",
            'positions'=>$post->positions,
            'school_id'=>$post->school_id,
            'staff_Status'=>$post->staff_Status,
            'staff_id'=>$post->staff_id,
            'staff_status'=>$post->staff_status,
            'status'=>$post->status,
            'subjects'=>$post->subjects,
            'teacher'=>$post->teacher,
            'user_address'=>$post->user_address,
            'user_age'=>$post->user_age,
            'user_alias'=>$post->user_alias,
            'user_card'=>$post->user_card,
            'user_head1'=>$post->user_head1,
            'user_head2'=>$post->user_head2,
            'user_id'=>$post->user_id,
            'user_image'=>$post->user_image,
            'user_iphone'=>$post->user_iphone,
            'user_mail'=>$post->user_mail,
            'user_name'=>$post->user_name,
            'user_openid'=>$post->user_openid,
            'user_sex'=>$post->user_sex,
            'user_word'=>$post->user_word,
            'whether'=>$post->whether,
            'work_number'=>$post->work_number
        ];*/
        $data = [
            'user_openid'=>$post->user_openid,
            'whether'=>$post->whether,
            'user_head1'=>$post->user_head1,
            'user_head2'=>1,
            'class_id'=>$post->class_id,
            'work_number'=>$post->work_number,
            'subjects'=>$post->subjects,
            'form_id'=>"xxx"
        ];
        $res = DB::table('fb_teacher_apply')->insert($data);
        if ($res){
            return response()->json([
                'msg'=>'ok'
            ]);
        }
    }

    public function addStudent(Request $post){
        $data=[
            'form_id'=>"xxx",
            'user_openid'=>$post->user_openid,
            'stu_number'=>$post->stu_number,
            'stu_images1'=>$post->stu_images1,
            'stu_images2'=>$post->stu_images2,
            'stu_images3'=>$post->stu_images3,
            'stu_head'=>$post->stu_head,
            'relation'=>''
        ];
        dd($data);
    }

    public function getStudent(Request $post){
        $openid=$post->user_openid;
        $student=DB::table('fb_student')->where('user_openid',$openid)->get();
        //dd($student);
        return response()->json([
            'msg'=>"ok",
            'data'=>$student
        ]);
    }
    public function likeStudent(Request $post){
        $name=$post->name;
        $student=DB::table('fb_class_message')->where('stu_name','like',"%".$name."%")->orWhere('stu_number','like',"%".$name."%")->get();
        foreach ($student as  $key=>$value){
            $classid=DB::table('fb_class_stu')->where('stu_number',$value->stu_number)->select('class_id')->first();
            if ($classid){
                $class=DB::table('fb_class')->where('class_id',$classid->class_id)->first();
                $value->class=$class->class_grade."年级".$class->class_name;
            }else{
                $value->class="暂无班级信息";
            }
        }
        return response()->json([
            'msg'=>"ok",
            'data'=>$student
        ]);
    }

    public function getOneStudent(Request $post){
        $stu_number=$post->stu_number;
        $info=DB::table('fb_class_message')->where('stu_number',$stu_number)->first();
        $classid=DB::table('fb_class_stu')->where('stu_number',$stu_number)->first();
        $class=DB::table('fb_class')->where('class_id',$classid->class_id)->first();
        if ($class){
            $student=DB::table('fb_student')->where('stu_number',$stu_number)->first();
            if ($student){
                $info->stu_head=$student->stu_head;
                $info->stu_image=$student->stu_images1;
            }else{
                $info->stu_head="";
                $info->stu_image="";
            }
            $info->class=$class->class_grade."年级".$class->class_name;
        }else{
            $info->class="暂无班级信息";
        }
        return response()->json([
            'msg'=>"ok",
            'data'=>$info
        ]);
    }

    public function exStudent(Request $post){
        $teacher=$post->user_openid;
        //dd($teacher);
        $classid=DB::table('fb_teacher_apply')->where('user_openid',$teacher)->select('class_id')->first();
        //dd($classid);
        $student=DB::table('fb_student')->where('class_id',$classid->class_id)->where('stu_status',0)->get();
        foreach ($student as $key=>$value){
            $parent=DB::table('fb_user')->where('user_openid',$value->user_openid)->first();
            $value->user=$parent;
        }
        return response()->json([
            'msg'=>"ok",
            'data'=>$student
        ]);
    }

    public function exStatus(Request $post){
        $stunum=$post->stu_number;
        $code=$post->code;
        $student=DB::table('fb_student')->where('stu_number',$stunum)->first();
        if ($code == 1){
            $res=DB::table('fb_student')->where('stu_number',$stunum)->update([
                'stu_status'=>$code
            ]);
            if ($res){

                $user=DB::table('fb_user')->where('user_openid',$student->user_openid)->first();
                $parent=[
                    'user_openid'=>$student->user_openid,
                    'stu_number'=>$student->stu_number,
                    'stu_name'=>$student->stu_name,
                    'parent_status'=>1,
                    'user_card'=>$user->user_card,
                    'creat_time'=>date('Y-m-d H:i:s',time()),
                    'form_id'=>'xxx',
                    'user_status'=>1,
                    'relation'=>$student->relation
                ];
                DB::table('fb_parent')->insert($parent);
                DB::table('fb_class_message')->where('stu_number',$stunum)->update([
                    'stu_image'=>$student->stu_images1
                ]);
                $this->Notice($student->stu_id);
                return response()->json([
                    'msg'=>'ok'
                ]);
            }
        }elseif($code == 2){
            $res=DB::table('fb_student')->where('stu_number',$stunum)->update([
                'stu_status'=>$code
            ]);
            $this->Notice($student->stu_id);
            if ($res){
                return response()->json([
                    'msg'=>"ok"
                ]);
            }
        }



    }

    public function getConfig(Request $post){
        $config=DB::table('config')->where('id',1)->first();

        return response()->json([
            'msg'=>"ok",
            'data'=>$config
        ]);
    }

    public function saveStudent(Request $post){
        $openid=$post->user_openid;
        $stunum=$post->stu_number;
        $user=DB::table('fb_user')->where('user_openid',$openid)->first();
        $student=DB::table('fb_class_message')->where('stu_number',$stunum)->first();
        $classid=DB::table('fb_class_stu')->where('stu_number',$student->stu_number)->first();
        $class=DB::table('fb_class')->where('class_id',$classid->class_id)->first();
        $data=[
            'user_openid'=>$post->user_openid,
            'user_name'=>$user->user_name,
            'user_iphone'=>$user->user_iphone,
            'stu_name'=>$student->stu_name,
            'stu_sex'=>$student->stu_sex,
            'stu_age'=>$student->stu_age,
            'stu_number'=>$post->stu_number,
            'stu_head'=>$post->stu_head,
            'class_id'=>$classid->class_id,
            'class_grade'=>$class->class_grade,
            'class_name'=>$class->class_name,
            'stu_images1'=>$post->stu_image,
            'stu_images2'=>1,
            'stu_images3'=>1,
            'relation'=>$post->relation,
            'stu_status'=>0,
            'form_id'=>"xx",
            'creat_time'=>date('Y-m-d H:i:s',time())
        ];
        //dd($data);
        $check=DB::table('fb_student')->where('user_openid',$openid)->where('stu_number',$stunum)->first();
        //dd($check);
        if (empty($check)){
            $res=DB::table('fb_student')->insert($data);
            if($res){
                return response()->json([
                    'msg'=>'ok'
                ]);
            }
        }else{
            $res=DB::table('fb_student')->where('stu_number',$post->stu_number)->update([
                'stu_head'=>$post->stu_head,
                'stu_images1'=>$post->stu_image,
                'relation'=>$post->relation,
                'stu_status'=>0,
            ]);
            if($res){
                return response()->json([
                    'msg'=>'ok'
                ]);
            }
        }

    }
    public function setNotify(Request $post)
    {
        $openId = $post->get('open_id');
        DB::table('fb_user')->where('user_openid','=',$openId)->update(['notify'=>1]);
        return response()->json([
            'msg'=>'ok'
        ]);
    }
    public function setSchoolNotify(Request $post)
    {
        $school = $post->get('school');
        $user_id = $post->get('user_id');
        $open_id = $post->get('open_id');
        if ($open_id){
            $user=DB::table('fb_user')->where('user_openid',$user_id)->first();
            if (!$user){
                return jsonResponse([
                    'msg'=>'error'
                ]);
            }
            $schoolNotify = SchoolNotify::where(
                'open_id','=',$open_id
            )->where('school','=',$school)->first();
            if ($schoolNotify){
                return jsonResponse([
                    'msg'=>'ok'
                ]);
            }
            $schoolNotify = new SchoolNotify();
            $schoolNotify->school = $school;
            $schoolNotify->open_id = $open_id;
            $schoolNotify->user_id = $user_id;
            $schoolNotify->save();
            return jsonResponse([
                'msg'=>'ok'
            ]);
        }
    }

}
