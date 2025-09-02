{{-- resources/views/partials/family-create-modal.blade.php --}}
<div v-if="openFamilyCreateModal==1" id="familyCreateModalOverlay" style="    position: fixed;
    top: 0px;
    left: 0px;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    display: flex
;
    justify-content: center;
    align-items: center;
    z-index: 10002;">
    <div id="familyCreateModal"
        style="background: rgb(255, 255, 255);
    width: 90%;
    max-width: 700px;
    max-height: 80vh;
    overflow-y: auto;
    border-radius: 8px;
    padding: 20px;
    font-family: Arial, sans-serif;
    position: relative;
    box-shadow: rgba(0, 0, 0, 0.3) 0px 2px 10px;">
        <span id="familyCreateModalClose" @click='closeModel()'
            style="position:absolute;top:5px;left:15px;font-size:28px;color:#888;cursor:pointer;user-select:none">×</span>

        <h3 style="margin:0 0 12px">إضافة عائلة</h3>
        <div id="familyCreateAlert" style="display:none;margin-bottom:10px"></div>

        <form id="familyCreateForm" method="POST" action="{{ route('family.store') }}">
            @csrf

            <input type="hidden" id="fam_building_id" name="building_id" :value="buildingCreated.id">
            {{-- If you want to tie family to an apartment later, you can add a dynamic select via JS --}}


            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label>الاسم </label>
                    <input type="text" class="form-control" name="name" id="fam_name" required>
                </div>
                <div>
                    <label>اسم العائلة</label>
                    <input type="text" class="form-control" name="family_name" id="fam_family_name" required>
                </div>
                {{-- شخصية اعتبارية؟ --}}
                <div style="grid-column:1 / span 2">
                    {{-- هيدن لإرسال 0 عند الإلغاء --}}
                    <input type="hidden" name="is_featured_person" value="0">
                    <label class="form-check form-switch" style="padding-right: 2.5rem;display:flex;align-items:center;gap:10px">
                        <input class="form-check-input" type="checkbox"
                               id="fam_is_featured_person" name="is_featured_person" value="1"
                               onchange="
                    var on=this.checked;
                    var wrap=document.getElementById('featured_person_wrap');
                    var inp=document.getElementById('fam_featured_person');
                    if(wrap) wrap.style.display = on ? 'block' : 'none';
                    if(inp){ inp.disabled = !on; if(!on) inp.value=''; }
               ">
                        <span style="
    margin-right: 40px;
">هل هو <strong>شخصية اعتبارية</strong>؟</span>
                    </label>
                </div>

                <div id="featured_person_wrap" style="grid-column:1 / span 2; display:none">
                    <label>اسم الشخصية الاعتبارية</label>
                    <input type="text" class="form-control" name="featured_person" id="fam_featured_person" disabled>
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
                    <input type="number" class="form-control" name="count_family_members" id="fam_count_family_members"
                        min="0">
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

                <hr>
                {{-- حقول بيانات رب الأسرة --}}
                <div style="grid-column:1 / span 2">
                    <label>إسـم رب الأسرة</label>
                    <input type="text" class="form-control" name="head_name" id="fam_head_name">
                </div>

                <div>
                    <label>الجنسية</label>
                    <input type="text" class="form-control" name="nationality" id="fam_nationality">
                </div>

                <div>
                    <label>مكان الولادة</label>
                    <input type="text" class="form-control" name="birth_place" id="fam_birth_place">
                </div>

                <div>
                    <label>تاريخ الولادة</label>
                    <input type="date" class="form-control" name="birth_date" id="fam_birth_date">
                </div>

                <div>
                    <label>القيد</label>
                    <input type="text" class="form-control" name="civil_registry" id="fam_civil_registry">
                </div>

                <div>
                    <label>الرقم الوطني</label>
                    <input type="text" class="form-control" name="national_id" id="fam_national_id">
                </div>
                <div style="grid-column:1 / span 2">
                    <label>مهنة الأب</label>
                    <input type="text" class="form-control" name="father_occupation" id="fam_father_occupation">
                </div>
                <div>
                    <label>الحالة</label>
                    <select class="form-select" name="status" id="fam_status">
                        <option value="published">منشور</option>
                        <option value="draft">مسودة</option>
                        <option value="pending">قيد الانتظار</option>
                    </select>
                </div>

                <hr>
                <div style="grid-column:1 / span 2">
                    <label>العنوان</label>
                    <input type="text" class="form-control" name="address" id="fam_address">
                </div>
                {{-- حالة الشقة فارغة؟ --}}
                <div>
                    {{-- hidden لإرسال 0 عند عدم التفعيل --}}
                    <input type="hidden" name="is_empty" value="0">
                    <label class="form-check form-switch" style="padding-right: 2.5rem; display:flex; align-items:center; gap:10px">
                        <input class="form-check-input" type="checkbox"
                               id="fam_is_empty" name="is_empty" value="1">
                        <span style="margin-right:40px">هل الشقة <strong>فارغة</strong>؟</span>
                    </label>
                </div>

                {{-- يحتاج مراجعة؟ --}}
                <div>
                    {{-- hidden لإرسال 0 عند عدم التفعيل --}}
                    <input type="hidden" name="need_review" value="0">
                    <label class="form-check form-switch" style="padding-right: 2.5rem; display:flex; align-items:center; gap:10px">
                        <input class="form-check-input" type="checkbox"
                               id="fam_need_review" name="need_review" value="1">
                        <span style="margin-right:40px">يحتاج إلى <strong>مراجعة</strong>؟</span>
                    </label>
                </div>

                <div style="grid-column:1 / span 2">
                    <label>ملاحظات</label>
                    <textarea class="form-control" rows="3" name="notes" id="fam_notes"></textarea>
                </div>
            </div>

            <div style="margin-top:16px;text-align:right">
                <button type="button" id="btnFamilyCancel" class="btn btn-secondary"
                    style="margin-right:8px">إلغاء</button>
                <button type="submit" id="btnFamilySave" class="btn btn-primary">حفظ العائلة</button>
            </div>
        </form>

    </div>
</div>
