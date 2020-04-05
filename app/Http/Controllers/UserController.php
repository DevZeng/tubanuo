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
            $user->user_head1=$teach->user_head1;
            $user->subjects=$teach->subjects;
            $user->status=$teach->status;
            return response()->json([
                'msg'=>'ok',
                'user'=>$user
            ]);
        }else{
            $worker=DB::table('fb_sch_staff')->where('user_openid',$openid)->select('staff_id','positions','date1','staff_status',"user_images1")->first();
            if ($worker){
                $user->positions=$worker->positions;
                $user->date1=$worker->date1;
                $user->user_images1=$worker->user_images1;
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
        if ($code == 1){
            $res=DB::table('fb_student')->where('stu_number',$stunum)->update([
                'stu_status'=>$code
            ]);
            if ($res){
                $student=DB::table('fb_student')->where('stu_number',$stunum)->first();
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
                    'stu_image'=>$student->stu_image
                ]);
                return response()->json([
                    'msg'=>'ok'
                ]);
            }
        }elseif($code == 2){
            $res=DB::table('fb_student')->where('stu_number',$stunum)->update([
                'stu_status'=>$code
            ]);
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
        $check=DB::table('fb_student')->where('user_openid',$openid)->where('stu_number',$stunum)->first();
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
                'stu_images1'=>$post->stu_images1,
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

}
