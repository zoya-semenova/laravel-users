<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends BaseController
{
    use ValidatesRequests;

    /**
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        $users = User::paginate(2);

        if ($request->ajax()) {
            return response()->json(['users' => $users, 'pagination' => (string)$users->links('pagination::bootstrap-5')], 200);
        }

        return view('users', compact('users'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, int $id = null)
    {
        $request->validate([
            'name'       => ['required', Rule::unique('users')->ignore($id)],
            'password' => [
                Rule::requiredIf(function() use($id) {
                    return empty($id);
                })
            ],
            'photo' => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ]);

        $fields = [
            'name' => $request->input('name'),
        ];
        if ($request->input('password')) {
            $fields['password'] = Hash::make($request->input('password'));
        }

        $user = User::updateOrCreate(['id' => $id], $fields);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');

            $fileName = $user->id . "." . $photo->getClientOriginalExtension();
            $request->file('photo')->move(public_path('user-photo'), $fileName);
            $user->update(['photo' => $fileName]);
        }

        return response()->json(['message'=>'Успешно','data' => $user], 200);

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

        return response()->json(['message'=>'Запись успешно удалена']);
    }
}
