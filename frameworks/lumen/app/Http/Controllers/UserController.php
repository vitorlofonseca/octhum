<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

    }

    public function createUser(Request $request){

        try{

            if (!$request->input('name'))
                throw new Exception('A name to user is necessary');

            if (!$request->input('id_resp_inc'))
                $request->input('id_resp_inc', 0);

            if (!$request->input('email'))
                throw new Exception('A email to user is necessary');

            if (!$request->input('password'))
                throw new Exception('A password to user is necessary');

            if (!$request->input('username'))
                throw new Exception('A username is necessary');

            $objUser = User::create($request->all());

            return response()->json($objUser);

        } catch (Exception $e){
            return response()->json($e->getMessage());
        }

    }

    public function getUser($id){

        try {

            $objUser = User::find($id);

            if (!is_object($objUser))
                throw new Exception('User\'s id invalid or doesn\'t exist');

            return response()->json($objUser);

        } catch (Exception $e){
            return response()->json($e->getMessage());
        }
    }

    public function deleteUser($id){

        try {

            $objUser = User::find($id);

            if (!is_object($objUser))
                throw new Exception('User\'s id invalid or doesn\'t exist');

            $objUser->delete();

            return response()->json('User deleted');

        } catch(Exception $e){

            return response()->json($e->getMessage());

        }

    }

    public function updateUser(Request $request,$id){

        try {

            $objUser = User::find($id);

            if (!is_object($objUser))
                throw new Exception('User\'s id invalid or doesn\'t exist');

            $objUser->name = $request->input('name') ? $request->input('name') : $objUser->name;
            $objUser->id_resp_alt = 0;
            $objUser->email = $request->input('email') ? $request->input('email') : $objUser->name;
            $objUser->username = $request->input('username') ? $request->input('username') : $objUser->name;
            $objUser->password = $request->input('password') ? $request->input('password') : $objUser->name;

            $objUser->save();

            return response()->json('User updated');

        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }


    public function getAllUsers(){

        $aObjUser = User::all();

        return response()->json($aObjUser);

    }

}
