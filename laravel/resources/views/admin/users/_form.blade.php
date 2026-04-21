@php
    $editing = isset($user);
@endphp

<div class="adminFormGrid">
    <div class="adminFormGroup">
        <label for="name">Nombre</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required>
        @error('name') <span class="adminFieldError">{{ $message }}</span> @enderror
    </div>

    <div class="adminFormGroup">
        <label for="last_name">Apellido</label>
        <input id="last_name" type="text" name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}" required>
        @error('last_name') <span class="adminFieldError">{{ $message }}</span> @enderror
    </div>

    <div class="adminFormGroup adminFormGroupWide">
        <label for="email">Correo</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
        @error('email') <span class="adminFieldError">{{ $message }}</span> @enderror
    </div>

    <div class="adminFormGroup adminFormGroupWide">
        <label for="role_id">Rol</label>
        <select id="role_id" name="role_id" required>
            <option value="">Selecciona un rol</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" @selected((int) old('role_id', $user->role_id ?? 0) === $role->id)>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
        @error('role_id') <span class="adminFieldError">{{ $message }}</span> @enderror
    </div>

    <div class="adminFormGroup">
        <label for="password">
            Contraseña {{ $editing ? '(dejar vacío para mantener)' : '' }}
        </label>
        <input id="password" type="password" name="password" {{ $editing ? '' : 'required' }}>
        @error('password') <span class="adminFieldError">{{ $message }}</span> @enderror
    </div>

    <div class="adminFormGroup">
        <label for="password_confirmation">Confirmar contraseña</label>
        <input id="password_confirmation" type="password" name="password_confirmation" {{ $editing ? '' : 'required' }}>
    </div>
</div>

<div class="adminFormActions">
    <a href="{{ route('admin.users.index') }}" class="adminUsersButton adminUsersButtonSecondary">Cancelar</a>
    <button type="submit" class="adminUsersButton adminUsersButtonPrimary">{{ $submitLabel }}</button>
</div>
