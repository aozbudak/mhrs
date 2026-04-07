<?php

namespace App\Support;

use App\Models\AppSetting;

class BildirimAyarlari
{
    private const KEY = 'appointment_attendance_notification';

    private const DEFAULT_DAYS_BEFORE = 1;

    private const DEFAULT_TEMPLATE = '{tarih} {saat} randevunuz ({doktor}) için katılımınızı onaylayın: Gelebilecek misiniz?';

    /**
     * @return array{days_before:int,message_template:string}
     */
    public static function get(): array
    {
        $row = AppSetting::query()->where('key', self::KEY)->first();
        $value = is_array($row?->value) ? $row->value : [];

        $daysBefore = (int) ($value['days_before'] ?? self::DEFAULT_DAYS_BEFORE);
        if ($daysBefore < 1 || $daysBefore > 30) {
            $daysBefore = self::DEFAULT_DAYS_BEFORE;
        }

        $template = trim((string) ($value['message_template'] ?? self::DEFAULT_TEMPLATE));
        if ($template === '') {
            $template = self::DEFAULT_TEMPLATE;
        }

        return [
            'days_before' => $daysBefore,
            'message_template' => $template,
        ];
    }

    public static function daysBefore(): int
    {
        return self::get()['days_before'];
    }

    public static function messageTemplate(): string
    {
        return self::get()['message_template'];
    }

    public static function save(int $daysBefore, string $messageTemplate): void
    {
        AppSetting::query()->updateOrCreate(
            ['key' => self::KEY],
            ['value' => [
                'days_before' => $daysBefore,
                'message_template' => $messageTemplate,
            ]]
        );
    }
}
