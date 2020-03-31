<?php

namespace App\Http\Controllers;

use APP\Modules\User\UserHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{
    //

    public function getUser(Request $post){
        $openid=$post->user_openid;
        $user=DB::table('fb_user')->where('user_openid',$openid)->first();
        if($user->teacher == 1){
            $teach=DB::table('fb_teacher_apply')->where('user_openid',$openid)->select('whether',"class_id",'work_number','subjects','status','creat_time')->first();
            $class=DB::table('fb_class')->where('class_id',$teach->class_id)->first();
            $user->class_grade=$class->class_grade;
            $user->class_name=$class->class_name;
            $user->work_number=$teach->work_number;
            $user->subjects=$teach->subjects;
            $user->status=$teach->status;
            return response()->json([
                'msg'=>'ok',
                'user'=>$user
            ]);
        }else{
            $worker=DB::table('fb_sch_staff')->where('user_openid',$openid)->select('staff_id','positions','date1','staff_status')->first();
            if ($worker){
                $user->positions=$worker->positions;
                $user->date1=$worker->date1;
                $user->staff_status=$worker->staff_status;
                return response()->json([
                    'msg'=>"ok",
                    'user'=>$user,
                ]);
            }else{
                return response()->json([
                    'msg'=>"ok",
                    'user'=>$user
                ]);
            }
        }
    }
    public function updateUser(Request $post){
        $userid=$post->user_id;
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

        if ($post->teacher == 1){
            $teacher=DB::table('fb_teacher_apply')->where('user_openid',$post->user_openid)->first();
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
                    'form_id'=>"xxx"
                ];
                DB::table('fb_teacher_apply')->insert($t_data);
            }else{
                $t_data=[
                    'user_openid'=>$post->user_openid,
                    'whether'=>$post->whether,
                    'user_head1'=>$post->user_head1,
                    'user_head2'=>$post->user_head2,
                    'class_id'=>$post->class_id,
                    'work_number'=>$post->work_number,
                    'subjects'=>$post->subjects,
                    'status'=>1,
                    'form_id'=>"xxx"
                ];
                DB::table('fb_teacher_apply')->where('user_openid',$post->user_openid)->update($t_data);
            }

        }else{
            if ($post->staff_status){
                $worker=DB::table('fb_sch_staff')->where('user_openid',$post->user_openid)->first();
                if ($worker !== null){
                    $w_data=[
                        'positions'=>$post->positions,
                        'user_images1'=>$post->user_images1,
                        'date1'=>$post->date1,
                        'staff_status'=>0,
                        'update_time'=>date('Y-m-d H:i:s',time())
                    ];
                    DB::table('fb_sch_staff')->where('user_openid',$post->user_openid)->update($w_data);
                }else{
                    $w_data=[
                        'user_openid'=>$post->user_openid,
                        'positions'=>$post->positions,
                        'user_images1'=>$post->user_images1,
                        'date1'=>$post->date1,
                        'staff_status'=>0,
                        'update_time'=>date('Y-m-d H:i:s',time())
                    ];
                    DB::table('fb_sch_staff')->insert($w_data);
                }
            }
        }
        $res=DB::table('fb_user')->where('userid',$userid)->update($data);
        if ($res){
            return response()->json([
                'msg'=>"ok"
            ]);
        }

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

}
