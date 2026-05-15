@csrf

<div class="adminUserForm">
    <div class="adminFormGrid">
        <div class="adminFormGroup adminFormGroupWide">
            <label class="adminFormLabel" for="name">Nombre</label>
            <input
                id="name"
                class="adminFormControl"
                type="text"
                name="name"
                value="{{ old('name', $category->name ?? '') }}"
                required
                maxlength="80"
            >
            @error('name')
                <span class="adminFieldError">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="adminFormActions">
        <a href="{{ route('admin.categories.index') }}" class="adminUsersButton adminUsersButtonSecondary">Cancelar</a>
        <button type="submit" class="adminUsersButton adminUsersButtonPrimary">{{ $buttonText }}</button>
    </div>
</div>
