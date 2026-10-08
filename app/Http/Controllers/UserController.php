<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        // Ordenamos: Primero Admins, luego Cajeros, luego Mozos
        $users = User::orderByRaw("FIELD(role, 'admin', 'cashier', 'waiter', 'kitchen', 'bar')")->paginate(10);

        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:255',
            'role' => 'required|in:admin,cashier,waiter,kitchen,bar',
        ], $this->validationMessages());

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $user->forceFill(['recoverable_password' => $request->password])->save();

        return redirect()->back()->with('success', 'Usuario registrado correctamente.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,cashier,waiter,kitchen,bar',
            'password' => 'nullable|string|min:8|max:255|confirmed',
            'current_password' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->filled('password') && (int) $user->id === (int) Auth::id())],
        ], $this->validationMessages());

        $this->verifyPreviousPassword($request, $user);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Solo actualizamos contraseña si el campo no está vacío
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->fill($data);
        if ($request->filled('password')) {
            $user->forceFill(['recoverable_password' => $request->password]);
        }
        $user->save();

        return redirect()->back()->with('success', 'Datos actualizados.');
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|max:255|confirmed',
            'current_password' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->filled('password') && (int) $user->id === (int) Auth::id())],
        ], $this->validationMessages());
        $this->verifyPreviousPassword($request, $user);
        unset($data['current_password']);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->fill($data);
        if ($request->filled('password')) {
            $user->forceFill(['recoverable_password' => $request->password]);
        }
        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function currentPassword(Request $request, User $user)
    {
        abort_unless($request->user()->role === 'admin', 403);
        $password = null;
        try {
            $stored = $user->recoverable_password;
            if (is_string($stored) && Hash::check($stored, $user->password)) {
                $password = $stored;
            }
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Una copia cifrada con otra clave nunca se muestra como contraseña válida.
        }
        Log::info('Consulta de contraseña de usuario', [
            'admin_id' => $request->user()->id,
            'user_id' => $user->id,
            'available' => $password !== null,
        ]);

        return response()->json([
            'password' => $password,
            'message' => $password === null
                ? 'Esta contraseña se guardó antes de habilitar la consulta. Establece una nueva para poder verla aquí.'
                : 'Contraseña actual guardada.',
        ])->header('Cache-Control', 'no-store, private')->header('Pragma', 'no-cache');
    }

    private function verifyPreviousPassword(Request $request, User $user): void
    {
        if ($request->filled('password') && $request->filled('current_password')
            && ! Hash::check($request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'La contraseña anterior no es correcta.',
            ]);
        }
    }

    private function validationMessages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya pertenece a otro usuario.',
            'password.confirmed' => 'La confirmación no coincide con la nueva contraseña.',
            'current_password.required' => 'Ingresa tu contraseña anterior para cambiarla.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'role.in' => 'Selecciona un rol válido.',
        ];
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta mientras estás conectado.');
        }

        // Opcional: Verificar si tiene ventas asociadas antes de borrar,
        // pero por simplicidad permitimos borrar (el historial queda con ID huerfano o se maneja en BD)
        $user->delete();

        return redirect()->back()->with('success', 'Usuario eliminado del sistema.');
    }
}
