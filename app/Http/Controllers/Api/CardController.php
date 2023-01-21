<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\PhoneNumber;
use App\Models\Link;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CardR;

class CardController extends Controller
{
    public function update(Request $request)
    {
        if(Auth::check()){

            $validator = Validator::make($request->all(), [
                'profile_image' => 'nullable|image|max:5120',
                'displayname' => 'required|string|max:20',
                'job_title' => 'required|string|max:100',
                'about' => 'required|string|max:255',
                //'email' => 'required|string|email|max:25',
                'address' => 'nullable|string|max:255',
                'phone_num1' => 'required|string|max:255',
                'phone_num2' => 'nullable|string|max:255',
                'linkedin' => 'required|string|max:255',
                'instagram' => 'nullable|string|max:255',
                'github' => 'nullable|string|max:255',
                'facebook' => 'nullable|string|max:255',
                'template_id'=>'required|numeric|digits:1'

            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }




            $user = auth()->user();
            $card = $user->card;

            if ($request->hasFile('profile_image')) {
                if (!file_exists(public_path('uploaded_images'))) {
                    mkdir(public_path('uploaded_images'), 0700, true);
                }
                $file = $request->file('profile_image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move('uploaded_images', $fileName);
            }

            $card->update([
                'profile_image' => url('/').'/uploaded_images/'.$fileName,
                'displayname' => $request['displayname'],
                'job_title' => $request['job_title'],
                'about' => $request['about'],
                'address' => $request['address'],

            ]);

            $ph = [$request['phone_num1'], $request['phone_num2']];

            $links = [$request['instagram'], $request['facebook'], $request['linkedin'], $request['github']];
            $phonetoedit=PhoneNumber::where('card_id','=',$card->id)->get();
            $i=0;
            foreach($phonetoedit as $phone){
                $phone->update(['number' => $ph[$i]]);
                if($i<2)
                    $i = $i + 1;
            }
            // if ($ph2) {
            //     update($id, $ph1, $ph2);
            // } else {
            //     PhoneNumberController::add($id, $ph1);
            // }
            $linktoedit=Link::where('card_id','=',$card->id)->get();
            $i=0;
            foreach ($linktoedit as $link) {
                $link->update(['link' =>$links[$i]]);
                $i = $i + 1;
            }



            // $card->fill($request->all());
            $card->profile_image = isset($fileName) ? url('/') . '/uploaded_images/' . $fileName : null;
            $card->save();
            return response()->json([
                'message' => 'The card has been saved successfully',
                'card' => $card
            ], 200);
        }
        else
        {
            return response()->json([
                "message" => "log in to continue."
            ],301);
        }
    }

    public function getCard(int $id){

        $card = Card::findOrFail($id);
        //$card = [$card, $card->user->email, $card->phonenumbers[0]->number, $card->phonenumbers[1]->number];

        $gg = new CardR($card);
        //dd($gg);
        return response()->json(['card'=>$gg]);
    }
    public function onlyCard(){ //only card
        $userId = auth()?->user()?->id;
        if($userId == null){
        return response()->json(['card'=>null]);

        }
        $card = Card::where('user_id','=',$userId)->get()->last();
        return response()->json(['card'=>$card]);
    }
}
