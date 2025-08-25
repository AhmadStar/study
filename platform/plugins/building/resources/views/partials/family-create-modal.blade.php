{{-- resources/views/partials/family-create-modal.blade.php --}}
<div v-if="openFamilyCreateModal==1" id="familyCreateModalOverlay"
    style="
background: rgb(255, 255, 255);width: 90%;max-width: 720px;max-height: 85vh;
overflow: auto;border-radius: 10px;left: 50%;top: 10%;
position: relative;padding: 18px;box-shadow: rgba(0, 0, 0, 0.25) 0px 8px 24px;">
    <div id="familyCreateModal"
        style="background: rgb(255, 255, 255);
    width: 90%;
    max-width: 720px;
    max-height: 92vh;
    overflow: auto;
    border-radius: 10px;
    position: relative;
    padding: 18px;
    box-shadow: rgba(0, 0, 0, 0.25) 0px 8px 24px;
    position: fixed;
    /* top: 10px; */
    left: 50%;
    top: 30px;
    z-index: 9999999999999;
    transform: translate(-50%);">
        <span id="familyCreateModalClose"
            style="position:absolute;top:10px;right:14px;font-size:28px;color:#888;cursor:pointer;user-select:none">×</span>

        <h3 style="margin:0 0 12px">إضافة عائلة</h3>
        <div id="familyCreateAlert" style="display:none;margin-bottom:10px"></div>

        <form id="familyCreateForm" method="POST" action="{{ route('family.store') }}">
    @csrf

    <input type="hidden" id="fam_building_id" name="building_id" :value="buildingCreated.id">
    {{-- If you want to tie family to an apartment later, you can add a dynamic select via JS --}}

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div>
            <label>اسم العائلة</label>
            <input type="text" class="form-control" name="family_name" id="fam_family_name" required>
        </div>
        <div>
            <label>رقم العائلة</label>
            <input type="text" class="form-control" name="family_number" id="fam_family_number">
        </div>

        <div>
            <label>رقم الطابق</label>
            <input type="number" class="form-control" name="floor_number" id="fam_floor_number" min="0">
        </div>
        <div>
            <label>رمز العائلة</label>
            <select class="form-select" name="family_code" id="fam_family_code">
                <option value="">—</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
            </select>
        </div>

        <div>
            <label>رقم الهاتف</label>
            <input type="text" class="form-control" name="phone" id="fam_phone">
        </div>
        <div>
            <label>عدد الأفراد</label>
            <input type="number" class="form-control" name="count_family_members" id="fam_count_family_members" min="0">
        </div>

        <div>
            <label>نوع ملكية العقار</label>
            <select class="form-select" name="house_type" id="fam_house_type">
                <option value="">—</option>
                <option value="ملك">ملك</option>
                <option value="ايجار">ايجار</option>
                <option value="غير ذلك">غير ذلك</option>
            </select>
        </div>
        <div>
            <label>الحالة</label>
            <select class="form-select" name="status" id="fam_status">
                <option value="published">منشور</option>
                <option value="draft">مسودة</option>
                <option value="pending">قيد الانتظار</option>
            </select>
        </div>

        <div style="grid-column:1 / span 2">
            <label>العنوان</label>
            <input type="text" class="form-control" name="address" id="fam_address">
        </div>

        <div style="grid-column:1 / span 2">
            <label>ملاحظات</label>
            <textarea class="form-control" rows="3" name="notes" id="fam_notes"></textarea>
        </div>
    </div>

    <div style="margin-top:16px;text-align:right">
        <button type="button" id="btnFamilyCancel" class="btn btn-secondary" style="margin-right:8px">إلغاء</button>
        <button type="submit" id="btnFamilySave" class="btn btn-primary">حفظ العائلة</button>
    </div>
</form>

    </div>
</div>
