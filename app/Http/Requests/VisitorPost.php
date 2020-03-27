<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisitorPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //$visitor->user_openid = $post->user_openid;
            //        $visitor->user_name = $post->user_name;
            //        $visitor->user_iphone = $post->user_iphone;
            //        $visitor->visitor_butt = $post->visitor_butt;
            //        $visitor->visitor_head1 = $post->visitor_head1;
            //        $visitor->visitor_reason = $post->visitor_reason;
            'user_openid'=>'required',
            'user_name'=>'required',
            'user_iphone'=>'required|numeric|digits_between:11,11',
            'visitor_butt'=>'required',
            'visitor_head1'=>'required',
            'visitor_reason'=>'required',
        ];
    }
    public function messages()
    {
        return [
            'user_openid.required'=>'openid不能为空！',
            'user_name.required'=>'姓名不能为空！',
            'user_iphone.required'=>'电话号码不能为空！',
            'user_iphone.numeric'=>'电话号码必须为数字类型！',
            'user_iphone.digits_between'=>'电话号码长度必须为11！',
            'visitor_butt.required'=>'被访人不能为空！',
            'visitor_head1.required'=>'头像不能为空！',
            'visitor_reason.required'=>'访问理由不能为空！',
        ];
    }
}
