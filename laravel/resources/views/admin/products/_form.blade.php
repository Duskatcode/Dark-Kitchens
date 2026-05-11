@csrf

<div class="adminUserForm">
    <div class="adminFormGrid">
        <div class="adminFormGroup">
            <label class="adminFormLabel" for="name">Nombre</label>
            <input
                id="name"
                class="adminFormControl"
                type="text"
                name="name"
                value="{{ old('name', $product->name ?? '') }}"
                required
                maxlength="120"
            >
            @error('name')
                <span class="adminFieldError">{{ $message }}</span>
            @enderror
        </div>

        <div class="adminFormGroup">
            <label class="adminFormLabel" for="category_id">Categoría</label>
            <select id="category_id" class="adminFormControl" name="category_id" required>
                <option value="">Selecciona una categoría</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id ?? 0) === $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <span class="adminFieldError">{{ $message }}</span>
            @enderror
        </div>

        <div class="adminFormGroup">
            <label class="adminFormLabel" for="price">Precio</label>
            <input
                id="price"
                class="adminFormControl"
                type="number"
                name="price"
                value="{{ old('price', $product->price ?? '') }}"
                required
                min="0.01"
                step="0.01"
            >
            @error('price')
                <span class="adminFieldError">{{ $message }}</span>
            @enderror
        </div>

        <div class="adminFormGroup">
            <label class="adminFormLabel" for="is_available">Disponibilidad</label>
            <select id="is_available" class="adminFormControl" name="is_available" required>
                <option value="1" @selected((string) old('is_available', isset($product) ? (int) $product->is_available : 1) === '1')>Disponible</option>
                <option value="0" @selected((string) old('is_available', isset($product) ? (int) $product->is_available : 1) === '0')>No disponible</option>
            </select>
            @error('is_available')
                <span class="adminFieldError">{{ $message }}</span>
            @enderror
        </div>

        <div class="adminFormGroup adminFormGroupWide">
            <label class="adminFormLabel" for="description">Descripción</label>
            <textarea
                id="description"
                class="adminFormControl"
                name="description"
                rows="5"
                required
                maxlength="1000"
            >{{ old('description', $product->description ?? '') }}</textarea>
            @error('description')
                <span class="adminFieldError">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="adminFormActions">
        <a href="{{ route('admin.products.index') }}" class="adminUsersButton adminUsersButtonSecondary">Cancelar</a>
        <button type="submit" class="adminUsersButton adminUsersButtonPrimary">{{ $buttonText }}</button>
    </div>
</div>
