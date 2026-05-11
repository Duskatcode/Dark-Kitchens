@csrf

<div class="adminUserForm">
    <div class="adminFormGrid">
        <div class="adminFormGroup adminFormGroupWide">
            <label class="adminFormLabel" for="name">Nombre del rol</label>
            <input
                id="name"
                class="adminFormControl"
                type="text"
                name="name"
                value="{{ old('name', $role->name ?? '') }}"
                required
                maxlength="80"
                placeholder="Ej: manager"
            >

            @error('name')
                <span class="adminFieldError">{{ $message }}</span>
            @enderror

            <p class="adminFormHint">
                Usa nombres en minúscula. Los roles base son admin, client y cook.
            </p>
        </div>
    </div>

    <div class="adminFormActions">
        <a href="{{ route('admin.roles.index') }}" class="adminUsersButton adminUsersButtonSecondary">Cancelar</a>
        <button type="submit" class="adminUsersButton adminUsersButtonPrimary">{{ $buttonText }}</button>
    </div>
</div>
