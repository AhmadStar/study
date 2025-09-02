<?php

namespace Botble\Building\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static BuildingTypeEnum SECURITY_POINT()
 * @method static BuildingTypeEnum RESIDENTIAL()
 * @method static BuildingTypeEnum COMMERCIAL()
 * @method static BuildingTypeEnum USABLE_HQ()
 * @method static BuildingTypeEnum SLAUGHTER_SITE()
 * @method static BuildingTypeEnum SECURITY_EVENT()
 * @method static BuildingTypeEnum MILITARY_POINT()
 */
class BuildingTypeEnum extends Enum
{
public const SECURITY_POINT = 'security_point';   // نقطة أمنية
public const RESIDENTIAL    = 'residential';      // بناء سكني
public const COMMERCIAL     = 'commercial';       // بناء تجاري
public const USABLE_HQ      = 'usable_hq';        // مقر قابل للاستخدام
public const SLAUGHTER_SITE = 'slaughter_site';   // موقع مجزرة
public const SECURITY_EVENT = 'security_event';   // حدث أمني
public const MILITARY_POINT = 'military_point';   // نقطة عسكرية

    // If you keep translations, this is ignored because we override label()/labels().
    public static $langPath = 'plugins/building::building.types';

    /**
     * Arabic labels for dropdowns etc.
     */
    public static function labels(): array
    {
        return [
            self::SECURITY_POINT => 'نقطة أمنية',
            self::RESIDENTIAL    => 'بناء سكني',
            self::COMMERCIAL     => 'بناء تجاري',
            self::USABLE_HQ      => 'مقر قابل للاستخدام',
            self::SLAUGHTER_SITE => 'موقع مجزرة',
            self::SECURITY_EVENT => 'حدث أمني',
            self::MILITARY_POINT => 'نقطة عسكرية',
        ];
    }

    /**
     * Single label (Arabic).
     */
    public function label(): string
    {
        return static::labels()[$this->value] ?? (string) $this->value;
    }

    public function toHtml(): HtmlString|string
    {
        $color = match ($this->value) {
            self::SECURITY_POINT => 'info',
            self::RESIDENTIAL    => 'success',
            self::COMMERCIAL     => 'primary',
            self::USABLE_HQ      => 'warning',
            self::SLAUGHTER_SITE => 'danger',
            self::SECURITY_EVENT => 'secondary',
            self::MILITARY_POINT => 'dark',
            default              => 'primary',
        };

return BaseHelper::renderBadge($this->label(), $color, icon: $this->getIcon());
    }

    public function getIcon(): string
{
    return match ($this->value) {
    self::SECURITY_POINT => 'ti ti-shield',
            self::RESIDENTIAL    => 'ti ti-home',
            self::COMMERCIAL     => 'ti ti-building-store',
            self::USABLE_HQ      => 'ti ti-building-community',
            self::SLAUGHTER_SITE => 'ti ti-alert-triangle',
            self::SECURITY_EVENT => 'ti ti-bell',
            self::MILITARY_POINT => 'ti ti-target',
            default              => 'ti ti-building',
        };
    }
}
