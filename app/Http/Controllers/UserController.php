<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\View\View;

class UserController extends BaseController
{
    use ValidatesRequests;

    /**
     * @return View
     */
    public function index()
    {
        $users = User::all();

        return view('users', ['users' => $users]);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'name'       => 'required|max:255',
            'password' => 'required',
        ]);

        $user = User::updateOrCreate([
            'id' => $request->id], [
            'name' => $request->name,
            'password' => $request->password
        ]);

        return response()->json(['message'=>'Запись успешно создана','data' => $user], 200);

    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name'       => 'required|max:255',
            'password' => 'required',
        ]);

        $user = User::find($id);

        if($user) {
            $user->name = $request->input('name');
            $user->password = $request->input('password');

            $user->update();

            return response()->json(['message' => 'Запись успешно обновлена', 'data' => $user], 200);
        }

        return response()->json(['message'=>'Not found'], 422);
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json($user);
        }

        return response()->json(['message'=>'Not found'], 422);

    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        User::find($id)->delete();

        return response()->json(['success'=>'Запись успешно удалена']);
    }
}
