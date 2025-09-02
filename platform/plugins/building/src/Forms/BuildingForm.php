<?php

namespace Botble\Building\Forms;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FormAbstract;
use Botble\Building\Enums\BuildingTypeEnum;
use Botble\Building\Http\Requests\BuildingRequest;
use Botble\Building\Models\Building;
use Botble\Area\Models\Area;

class BuildingForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Building::class)
            ->setValidatorClass(BuildingRequest::class)
            ->add('area_id', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('القطاع')
                ->choices(Area::pluck('name', 'id')->toArray())
                ->required())

            ->add('building_type', \Botble\Base\Forms\Fields\SelectField::class, SelectFieldOption::make()
                ->label('نوع البناء')
                ->placeholder('اختر النوع…')
                ->choices(\Botble\Building\Enums\BuildingTypeEnum::labels())
                ->searchable()
                ->required())

            ->add('name', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('رقم البناء')
                ->required())
            ->add('floors_count', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
                ->label('عدد الطوابق'))
            ->add('latitude', \Botble\Base\Forms\Fields\HiddenField::class, TextFieldOption::make()
                ->label('خط العرض'))
            ->add('longitude', \Botble\Base\Forms\Fields\HiddenField::class, TextFieldOption::make()
                ->label('خط الطول'))
            ->add('map', \Botble\Base\Forms\Fields\HtmlField::class, \Botble\Base\Forms\FieldOptions\HtmlFieldOption::make()
                ->label('الموقع على الخريطة')
                ->content('
        <div id="map" style="height: 400px; width: 100%;"></div>

        <script>
            function initMap() {
                let lat = parseFloat(document.querySelector("input[name=\'latitude\']").value) || 33.481289;
                let lng = parseFloat(document.querySelector("input[name=\'longitude\']").value) || 36.311463;

                let map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat: lat, lng: lng },
                    zoom: 17,
                    mapTypeId: google.maps.MapTypeId.SATELLITE
                });

                let marker = new google.maps.Marker({
                    position: { lat: lat, lng: lng },
                    map: map,
                    draggable: true,
                });

                google.maps.event.addListener(marker, "dragend", function (event) {
                    document.querySelector("input[name=\'latitude\']").value = event.latLng.lat();
                    document.querySelector("input[name=\'longitude\']").value = event.latLng.lng();
                });

                map.addListener("click", function(event) {
                    marker.setPosition(event.latLng);
                    document.querySelector("input[name=\'latitude\']").value = event.latLng.lat();
                    document.querySelector("input[name=\'longitude\']").value = event.latLng.lng();
                });
            }
        </script>

        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBokt_jID9DLiGm7hbjYfVojPRUnXE-2ig&callback=initMap"
        async defer></script>
    '))

            // ->add('address', \Botble\Base\Forms\Fields\TextField::class, TextFieldOption::make()
            //     ->label('العنوان'))
            // ->add('description', \Botble\Base\Forms\Fields\TextareaField::class, TextFieldOption::make()
            //     ->label('الوصف'))
            ->add('is_empty', \Botble\Base\Forms\Fields\RadioField::class, \Botble\Base\Forms\FieldOptions\RadioFieldOption::make()
    ->label('هل البناء فارغ؟')
    ->choices([
        1 => 'نعم',
        0 => 'لا',
    ])) // ✅ القيمة الافتراضية

            ->add('status', \Botble\Base\Forms\Fields\SelectField::class, StatusFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}
