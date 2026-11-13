<?php

namespace App\Filament\Resources\TrackerSuppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Xint0\WialonPhp\Wialon;
use Xint0\WialonPhp\WialonException;

class TrackerSuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                Action::make('ImportHw')
                    ->label('Загрузка оборудования из Wialon')
                    ->action(function (array $data){
                        // You can specify a custom host.
                        $baseUri = 'https://monitor.armatel.com.ua';
                        $server_id = 4;

                        $wialon = new Wialon($baseUri);
                        try {
                           $token = '9315ba1be574bb2c2b76a4273be169a75249C0B731B79F5FB8D6C597537E0C57B5868C2F';
                            $wialon->login($token);
                            // After logging in to service a session ID is returned.
                            $params = [
                                'spec' => [
                                    'itemsType' => 'avl_hw',
                                    'propName' => 'rel_hw_type_name',
                                    'propValueMask' => '*',
                                    'sortType' => '',
                                    'propType' => '',
                                    'or_logic' => false,
                                ],
                                'force' => 1,
                                'flags' => 0x01,
                                'from' => 0,
                                'to' => 0,
                            ];
                            $objects = $wialon->unitByName($params);
                            dd($objects);
                            foreach ($objects['items'] as $object){
                                //dd($object);
                                $record = array(
                                    //'client_id' => $client_id,
                                    //'user_id' => Auth::user()->id,
                                    'name'  => $object['nm'],
                                    'imei' => $object['uid'],
                                    'sim_number' => $object['ph'],
                                    'tracker_supplier_id' => $object['hw'],
                                    'server_id' => $server_id,
                                    'last_position' => '',
                                    'last_sync' => now(),
                                    'tarif_id' => 1,
                                    'sleep_tarif_id' => 3,

                                );
                                $cur_operation = Auto::create($record);
                            }

                            

                        } catch (WialonException $exception) {
                        // WialonException is thrown when request fails or service returns error response.
                        }
                        


                    })

            ]);
    }
}
