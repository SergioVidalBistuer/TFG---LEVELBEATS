<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ColeccionBeatSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('coleccion_beat')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $colecciones = DB::table('coleccion')->pluck('id', 'titulo_coleccion');
        $beats = DB::table('beat')->pluck('id', 'titulo_beat');

        $relaciones = [
            'Melodic Trap Essentials' => [
                'Lil Baby x Melodic Trap Type Beat - Smokin',
                'Lil Tjay x Melodic Trap Type Beat - I Told You',
                'Lil Tjay x PnB Rock Type Beat - Breath Away',
                'PnB Rock x Lil Tjay Type Beat - Pastime',
                'PnB Rock x Lil Tjay Type Beat - Voices',
                'Young Thug x Melodic Trap Type Beat - Gotti',
            ],
            'UK Drill Pack' => [
                'Kidd Keo Type Beat - Codeine',
                'Kwengface x DigDat x C1 UK Drill Type Beat - Wrapped',
                'Lil Baby x Meek Mill x Quay Global Type Beat - Aretha',
                'MC Igu x Derek Type Beat - Pipe',
                'OFB SJ x BandoKay x Double L z Type Beat - Half It',
                'RV x Ghosty UK Drill Type Beat - Underground',
            ],
            'Piano / Sad Pack' => [
                'Emo Rap x LoFi Type Beat - Let Me Down',
                'Guitar Poorstacy x Iann Dior Type Beat - Isolated',
                'Iann Dior x Juice WRLD Pop Guitar Type Beat - Have Fun',
                'Sad Piano Juice WRLD Type Beat - Affection',
                'Sad XXXTentacion x Killstation Type Beat - Plague',
                'Sad XXXTentacion x NF Piano Type Beat - Relapse',
            ],
            'Trap Hits' => [
                'Lil Tjay x PnB Rock Type Beat - I ll Be There',
                'Nav x The Weeknd Type Beat - I Don t Sleep At Night',
                'Polo G x Lil Baby Type Beat - Patek',
                'Polo G x Lil Tjay Type Beat - Millions',
                'Roddy Ricch x Mustard Type Beat - Fly Away',
                'Young Thug x Tory Lanez Type Beat - Otherside',
            ],
            'Club / Deep House' => [
                'Amargue - Deep Space Trap Type Beat',
                'Eldzhei x Nebezao - Deep House Club Trap Beat',
                'Lil Mosey x Lil Skies x Lil Tecca Type Beat - Cold Like',
                'Speed Melodic Type Beat - Trap Instrumental 2020',
                'Travis Scott Type Beat 2019 - Callejero',
                'Travis Scott x FRVRFRIDAY Type Beat - Fantasia',
            ],
        ];

        foreach ($relaciones as $tituloColeccion => $titulosBeat) {
            $idColeccion = $colecciones[$tituloColeccion] ?? null;

            if (!$idColeccion) {
                throw new RuntimeException("No existe la colección: {$tituloColeccion}");
            }

            foreach ($titulosBeat as $tituloBeat) {
                $idBeat = $beats[$tituloBeat] ?? null;

                if (!$idBeat) {
                    throw new RuntimeException("No existe el beat: {$tituloBeat}");
                }

                DB::table('coleccion_beat')->insert([
                    'id_coleccion' => $idColeccion,
                    'id_beat' => $idBeat,
                ]);
            }
        }
    }
}
