<?php

namespace App\Console\Commands;

use App\Clients\MsClient;
use App\Services\AdditionalServices\AttributeService;
use App\Services\Settings\SettingsService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Console\Command;

class updateDocument extends Command
{

    protected $signature = 'updateDocument:create';

    protected $description = 'Обновление доп полей в документах';
    private SettingsService $settingsService;

    public function __construct(settingsService $settingsService)
    {
        $this->settingsService = $settingsService;
        parent::__construct();
    }
    public function handle(): void
    {
        $allSettings = $this->settingsService->getSettings();
        foreach ($allSettings as $settings) {
            try {
                $ClientCheckMC = new MsClient($settings->TokenMoySklad);
                $ClientCheckMC->get('https://api.moysklad.ru/api/remap/1.2/entity/employee');
            } catch (BadResponseException $e) {continue;}

            $data = [
                "tokenMs" => $settings->TokenMoySklad,
                "accountId" => $settings->accountId,
            ];

            dispatch(function () use ($data) {
                app(AttributeService::class)->setAllAttributesMs($data);
            })->onQueue('default');

            $this->info('successfully is accountId: '. $settings->accountId);
        }


    }

}
