@extends('core/base::layouts.master')

@section('content')
<div class="container">
    <h2>إضافة عائلة</h2>

    <form action="{{ route('families.update', $building->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">الاسم </label>
            <input type="text" name="family_name" value="" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">اسم العائلة</label>
            <input type="text" name="family_name" value="" class="form-control">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">رقم العائلة</label>
            <input type="text" name="family_number" value="" class="form-control">
        </div>
    </div>
        {{-- شخصية اعتبارية؟ --}}
        <div style="grid-column:1 / span 2">
            {{-- هيدن لإرسال 0 عند الإلغاء --}}
            <input type="hidden" name="is_featured_person" value="0">
            <label class="form-check form-switch" style="display:flex;align-items:center;gap:10px">
                <input class="form-check-input" type="checkbox"
                       id="fam_is_featured_person" name="is_featured_person" value="1"
                       onchange="
                    var on=this.checked;
                    var wrap=document.getElementById('featured_person_wrap');
                    var inp=document.getElementById('fam_featured_person');
                    if(wrap) wrap.style.display = on ? 'block' : 'none';
                    if(inp){ inp.disabled = !on; if(!on) inp.value=''; }
               ">
                <span>هل هو <strong>شخصية اعتبارية</strong>؟</span>
            </label>
        </div>

        <div id="featured_person_wrap" style="grid-column:1 / span 2; display:none">
            <label>اسم الشخصية الاعتبارية</label>
            <input type="text" class="form-control" name="featured_person" id="fam_featured_person" disabled>
        </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">رقم الطابق</label>
            <input type="text" name="floor_number" value="" class="form-control">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">معرف الشقة</label>
            <input type="text" name="apartment_id" value="" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">رمز العائلة</label>
            <select name="family_code" class="form-control">
                @foreach(['A','B','C','D','E'] as $code)
                    <option value="{{ $code }}" >{{ $code }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">العنوان</label>
            <input type="text" name="address" value="" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">المنطقة</label>
            <input type="text" name="region_id" value="" class="form-control">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">رقم الهاتف</label>
            <input type="text" name="phone" value="" class="form-control">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">ملاحظات</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-control">
                <option value="active" >نشط</option>
                <option value="inactive" >غير نشط</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">عدد أفراد العائلة</label>
            <input type="number" name="count_family_members" value="" class="form-control">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">البناء</label>
            <select name="building_id" class="form-control">
                @foreach(\Botble\Building\Models\Building::orderBy('name')->get() as $building)
                    <option value="{{ $building->id }}"
                            {{ old('building_id', request('id')) == $building->id ? 'selected' : '' }}>
                        {{ $building->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">نوع ملكية العقار</label>
            <select name="house_type" class="form-control">
                <option value="ملك" >ملك</option>
                <option value="إيجار" >إيجار</option>
                <option value="غير ذلك" >غير ذلك</option>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
</form>
</div>
@endsection
