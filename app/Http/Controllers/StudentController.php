<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2020/7/14
 * Time: 11:22
 */

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function getUserStudent(Request $post){
        $openid=$post->user_openid;
        $data=DB::table('fb_parent')->where('user_openid',$openid)->get();
        return jsonResponse([
            'msg'=>'ok',
            'data' => $data
        ]);
    }

    public function getExamName(Request $post){
        $number=$post->number;
        $select = ['id','sch_num','k_name','k_date'];
        $data=DB::table('fb_score')->where('sch_num',$number)->select($select)->orderBy('k_date', 'desc')->get();
        return jsonResponse([
            'msg'=>'ok',
            'data' => $data
        ]);
    }

    public function getExamDetail(Request $post){
        $data = DB::table('fb_score')->where('id',$post->id)->where('sch_num',$post->number)->orderBy('k_date', 'desc')->first();
        if(strlen($data->chineses) >= 1){
            $data->chineses = [
                'score' => $data->chineses,
                'text' => '语文'
            ];
        }else{
            unset($data->chineses);
        }
        if(strlen($data->mathematics) >= 1){
            $data->mathematics = [
                'score' => $data->mathematics,
                'text' => '数学'
            ];
        }else{
            unset($data->mathematics);
        }
        if(strlen( $data->english) >= 1){
            $data->english = [
                'score' => $data->english,
                'text' => '英语'
            ];
        }else{
            unset($data->english);
        }
        if(strlen($data->english_k) >= 1){
            $data->english_k = [
                'score' => $data->english_k,
                'text' => '口语'
            ];
        }else{
            unset($data->english_k);
        }
        if(strlen($data->politics) >= 1){
            $data->politics = [
                'score' => $data->politics,
                'text' => '政治'
            ];
        }else{
            unset($data->politics);
        }
        if(strlen($data->biology) >= 1){
            $data->biology = [
                'score' => $data->biology,
                'text' => '生物'
            ];
        }else{
            unset($data->biology);
        }
        if(strlen($data->physics) >= 1){
            $data->physics = [
                'score' => $data->physics,
                'text' => '物理'
            ];
        }else{
            unset($data->physics);
        }
        if(strlen($data->chemistry) >= 1){
            $data->chemistry = [
                'score' => $data->chemistry,
                'text' => '化学'
            ];
        }else{
            unset($data->chemistry);
        }
        if(strlen($data->sports) >= 1){
            $data->sports = [
                'score' => $data->sports,
                'text' => '体育'
            ];
        }else{
            unset($data->sports);
        }
        if(strlen($data->li_zong) >= 1){
            $data->li_zong = [
                'score' => $data->li_zong,
                'text' => '理综'
            ];
        }else{
            unset($data->li_zong);
        }
        if(strlen($data->wen_zong) >= 1){
            $data->wen_zong = [
                'score' => $data->wen_zong,
                'text' => '文综'
            ];
        }else{
            unset($data->wen_zong);
        }
        if(strlen($data->art) >= 1){
            $data->art = [
                'score' => $data->art,
                'text' => '艺考'
            ];
        }else{
            unset($data->art);
        }
        return jsonResponse([
            'msg'=>'ok',
            'data' => $data
        ]);
    }
}