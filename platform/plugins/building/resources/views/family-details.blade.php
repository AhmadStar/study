@extends('core/base::layouts.master')

@section('content')
<div class="container">
    <h2>تعديل بيانات العائلة</h2>

    <form action="{{ route('families.update', $family->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">اسم العائلة</label>
            <input type="text" name="family_name" value="{{ old('family_name', $family->family_name) }}" class="form-control">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">رقم العائلة</label>
            <input type="text" name="family_number" value="{{ old('family_number', $family->family_number) }}" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">رقم الطابق</label>
            <input type="text" name="floor_number" value="{{ old('floor_number', $family->floor_number) }}" class="form-control">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">معرف الشقة</label>
            <input type="text" name="apartment_id" value="{{ old('apartment_id', $family->apartment_id) }}" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">رمز العائلة</label>
            <select name="family_code" class="form-control">
                @foreach(['A','B','C','D','E'] as $code)
                    <option value="{{ $code }}" {{ old('family_code', $family->family_code) == $code ? 'selected' : '' }}>{{ $code }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">العنوان</label>
            <input type="text" name="address" value="{{ old('address', $family->address) }}" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">المنطقة</label>
            <input type="text" name="region_id" value="{{ old('region_id', $family->region_id) }}" class="form-control">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">رقم الهاتف</label>
            <input type="text" name="phone" value="{{ old('phone', $family->phone) }}" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">ملاحظات</label>
            <textarea name="notes" class="form-control">{{ old('notes', $family->notes) }}</textarea>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-control">
                <option value="active" {{ old('status', $family->status) == 'active' ? 'selected' : '' }}>نشط</option>
                <option value="inactive" {{ old('status', $family->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">عدد أفراد العائلة</label>
            <input type="number" name="count_family_members" value="{{ old('count_family_members', $family->count_family_members) }}" class="form-control">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">معرف البناء</label>
            <input type="text" name="building_id" value="{{ old('building_id', $family->building_id) }}" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">نوع ملكية العقار</label>
            <select name="house_type" class="form-control">
                <option value="ملك" {{ old('house_type', $family->house_type) == 'ملك' ? 'selected' : '' }}>ملك</option>
                <option value="إيجار" {{ old('house_type', $family->house_type) == 'إيجار' ? 'selected' : '' }}>إيجار</option>
                <option value="غير ذلك" {{ old('house_type', $family->house_type) == 'غير ذلك' ? 'selected' : '' }}>غير ذلك</option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
</form>
</div>
@endsection
