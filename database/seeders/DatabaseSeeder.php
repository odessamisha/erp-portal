<?php

namespace Database\Seeders;

use App\Models\Auto;
use App\Models\City;
use App\Models\Client;
use App\Models\Exchange;
use App\Models\Installer;
use App\Models\Manager;
use App\Models\Provider;
use App\Models\Server;
use App\Models\SimSupplier;
use App\Models\Tarif;
use App\Models\Tracker;
use App\Models\TrackerSupplier;
use App\Models\OperationType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => '123456',
            'role' => 1
        ]);

        User::factory()->create([
            'name' => 'User',
            'email' => 'user@mail.com',
            'password' => '123456',
            'role' => 0
        ]);

        SimSupplier::factory()->create([
            'name' => 'Vodafone',
            'description' => 'Vodafone UA',
        ]);

        SimSupplier::factory()->create([
            'name' => 'Kyivstar',
            'description' => 'Kyivstar UA',
        ]);

        SimSupplier::factory()->create([
            'name' => 'Vodafone Global',
            'description' => 'Vodafone Global UA',
        ]);

        Server::factory()->create([
           'name' => 'Armatel GPS',
           'url' => 'https://gps.armatel.com.ua',
           'token' => 'b9b6da464e7b82c62c74ef90f31e71853166D0FA77A2135462D575BBD5290FCE10811BDD'
        ]);

        Server::factory()->create([
            'name' => 'Armatel Local1',
            'url' => 'https://local.armatel.com.ua',
            'token' => '6ab2bb816205095078345f7aa879f86eA4BBE88E600E87A75E445456BDC2775D0B19F3F4'
        ]);

        Server::factory()->create([
            'name' => 'Armatel Local3',
            'url' => 'https://local3.armatel.com.ua',
            'token' => 'b483de06e7635171eaea88ed13bc7f748107BF65EBCBD1593EC7B2AC5EB76DADD93FBE8E'
        ]);

        Server::factory()->create([
            'name' => 'Armatel Monitor1',
            'url' => 'https://monitor.armatel.com.ua',
             'token' => '9315ba1be574bb2c2b76a4273be169a75249C0B731B79F5FB8D6C597537E0C57B5868C2F'
        ]);

        Server::factory()->create([
            'name' => 'Armatel Monitor2',
            'url' => 'https://monitor2.armatel.com.ua',
            'token' => 'e70754e34ce04b4782a4bdeeb74c2129BF64855944D1A90B9A4059E2DC88DFD1B597DADD'
        ]);

        Server::factory()->create([
            'name' => 'Armatel Monitor3',
            'url' => 'https://monitor3.armatel.com.ua',
            'token' => '2a6a3970360d578fcc2c11af8ca04057056162E21C8DA52B9CF6C7275EA88456CFE3129B'
        ]);

        Server::factory()->create([
            'name' => 'Armatel Hosting',
            'url' => 'https://hst-api.wialon.eu',
            'token' => 'e8daa17d7b0020a2c348744eea98c53e085ABCC0110FFE8252D39B1ACEE8763369038C9B'
        ]);

        City::factory()->create([
           'name' => 'Одесса',
        ]);

        City::factory()->create([
            'name' => 'Днепр',
        ]);

        City::factory()->create([
            'name' => 'Киев',
        ]);

        City::factory()->create([
            'name' => 'Винница',
        ]);

        Provider::factory()->create([
           'name' => 'ФОП Станков Артем (без договора)',
           'iban' => 'UA823052990000026003034929005',
            'token' => '71b117ee-def2-4a05-8de9-6939a9530513IIGGuIrUjVWAqz0BqvX3yLz5m8nRGCvHrm4EySZGA/WuwdvVc+ipfX0f53uLHegl2Z6q6hRK789tOb5SCCy/qLQbq+RCFm9GQDiuOr/rchBHkaoeSO8RsxP1DmLodxTJqzn+OTlxqngJH1AebHc5UC49lzDquFHDKyaIfzPR39PGvUmbSXJl6k8yauTW22F5QLY0jEEITr0ZY81r+g5kFDgbA2Sr14nmqr5Jpgr7f3htGD26q73tH7TKMS+dyi82OvlcAPs='
        ]);

        Provider::factory()->create([
            'name' => 'ФОП Станков Артем (по договору)',
            'iban' => 'none'
        ]);

        Provider::factory()->create([
            'name' => 'ФОП Станков Степан (по договору)',
            'iban' => 'none'
        ]);

        Provider::factory()->create([
            'name' => 'ФОП Станков Михаил (по договору)',
            'iban' => 'none'
        ]);

        Provider::factory()->create([
            'name' => 'ФОП Желяскова Елена (по договору)',
            'iban' => 'none'
        ]);

        Exchange::factory()->create([
            'name' => 'Viber'
        ]);

        Exchange::factory()->create([
            'name' => 'Telegram'
        ]);

        Exchange::factory()->create([
            'name' => 'Whatsapp'
        ]);

        Exchange::factory()->create([
            'name' => 'Fredo'
        ]);

        Exchange::factory()->create([
            'name' => 'Вчасно'
        ]);

        Exchange::factory()->create([
            'name' => 'Новая Почта'
        ]);

        Exchange::factory()->create([
            'name' => 'УкрПочта'
        ]);

        Exchange::factory()->create([
            'name' => 'Лично в руки'
        ]);

        Manager::factory()->create([
            'name' => 'Михаил',
            'description' => 'Супер Менеджер'
        ]);

        Manager::factory()->create([
            'name' => 'Сергей Винница',
            'description' => 'Тел. 067-0000-000-00'
        ]);

        Manager::factory()->create([
            'name' => 'Демьян Ровно',
            'description' => 'Тел. 067-0000-000-00'
        ]);

        Tarif::factory()->create([
           'name' => 'Standart Tarif (180)',
           'price' => 180
        ]);

        Tarif::factory()->create([
            'name' => 'Individual Tarif (150)',
            'price' => 150
        ]);

        Tarif::factory()->create([
            'name' => 'Sleep (60)',
            'tarif_type' => 'sleep',
            'price' => 60
        ]);

        Client::factory()->create([
            'name' => 'PupkinTrans',
            'username' => 'client_1',
            'server_id' => 1,
            'provider_id' =>2,
            'manager_id' =>1,
            'city_id' =>1,
            'address' => 'addr1',
            'contact_name' => 'contract1',
            'contact_phone' => '+38011111',
            'contract_number' => '11111',
            'exchange_id' => 1,
            'balance' => -100,
            'user_id' => 1
        ]);

        Client::factory()->create([
            'name' => 'Autoline',
            'username' => 'client_2',
            'server_id' => 2,
            'provider_id' =>1,
            'manager_id' =>1,
            'city_id' =>1,
            'address' => 'addr1',
            'contact_name' => 'contract2',
            'contact_phone' => '+38033333',
            'contract_number' => '11111',
            'exchange_id' => 1,
            'balance' => -100,
            'user_id' => 1
        ]);

        Client::factory()->create([
            'name' => 'CargoChina',
            'username' => 'client_3',
            'server_id' => 2,
            'provider_id' =>1,
            'manager_id' =>1,
            'city_id' =>1,
            'address' => 'addr3',
            'contact_name' => 'contract3',
            'contact_phone' => '+38033333',
            'contract_number' => '11111',
            'exchange_id' => 1,
            'user_id' => 1
        ]);

        Installer::factory()->create([
            'name' => 'Igor',
            'contact_number' => '+38011111',
        ]);

        Installer::factory()->create([
            'name' => 'Vlad',
            'contact_number' => '+38011111',
        ]);

        Installer::factory()->create([
            'name' => 'Artem',
            'contact_number' => '+38011111',
        ]);

        TrackerSupplier::factory()->create([
           'name' => 'None',
           'description' => 'Стартовый Тип',
        ]);

        TrackerSupplier::factory()->create([
           'name' => 'BI-530Cv3',
           'description' => 'Bitrek BI-530Cv3',
        ]);

        TrackerSupplier::factory()->create([
            'name' => 'BI-530R',
            'description' => 'Bitrek BI-530R',
        ]);

        TrackerSupplier::factory()->create([
            'name' => 'FMB125int',
            'description' => 'Teltonika',
        ]);

        OperationType::factory()->create([
            'name' => 'Payroll Automat',
            'operation_flow' => 'payroll',
            'description' => 'Автоматическое начисление',
        ]);
        
        OperationType::factory()->create([
            'name' => 'Incoming Bank',
            'operation_flow' => 'incoming',
            'description' => 'Поступление из Банка',
        ]);

         OperationType::factory()->create([
            'name' => 'Incoming Cash',
            'operation_flow' => 'incoming',
            'description' => 'Поступление к кассу',
        ]);
        

        

    }
}
