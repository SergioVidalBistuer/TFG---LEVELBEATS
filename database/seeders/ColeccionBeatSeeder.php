<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColeccionBeatSeeder extends Seeder
{
    public function run(): void
    {
        $colecciones = DB::table('coleccion')->pluck('id', 'titulo_coleccion');
        $beats = DB::table('beat')->pluck('id', 'titulo_beat');

        $melodic = $colecciones['Melodic Trap Essentials'] ?? null;
        $drill   = $colecciones['UK Drill Pack'] ?? null;
        $club    = $colecciones['Club / Deep House'] ?? null;
        $trap    = $colecciones['Trap Hits'] ?? null;
        $piano   = $colecciones['Piano / Sad Pack'] ?? null;

        if (!$melodic || !$drill || !$club || !$trap || !$piano) {
            throw new \Exception("Faltan colecciones. Ejecuta ColeccionSeeder antes.");
        }

        $attach = function (int $idColeccion, array $titulos) use ($beats) {
            foreach ($titulos as $t) {
                if (!isset($beats[$t])) {
                    throw new \Exception("No existe el beat en BD con título exacto: {$t}");
                }
                DB::table('coleccion_beat')->updateOrInsert([
                    'id_coleccion' => $idColeccion,
                    'id_beat' => $beats[$t],
                ], []);
            }
        };

        // 1) Melodic Trap Essentials (6)
        $attach($melodic, [
            'Lil Tjay x Melodic Trap Type Beat - I Told You (Prod. Ilgu)',
            'Lil Tjay x PnB Rock Type Beat - Breath Away Melodic Trap Type Beat - Instrumental',
            'PnB Rock x Lil Tjay Type Beat - Pastime Melodic Trap Type Beat - Instrumental',
            'PnB Rock x Lil Tjay Type Beat - Voices Melodic Trap Type Beat - Instrumental',
            'Polo g x Lil Baby Type Beat - PATEK Melodic Trap Beat Instrumental',
            'Young Thug x Melodic Trap Type Beat - Gotti (Prod. Ilgu)',
        ]);

        // 2) UK Drill Pack (6)
        $attach($drill, [
            'OFB SJ x BandoKay x Double L\'z Type Beat Half It (Prod. Yoni)',
            'kwengface x DigDat x C1 UK Drill Type Beat - WRAPPED (PROD BY 3LACKONDABEAT)',
            'RV x GHOSTY UK DRILL TYPE BEAT - UNDERGROUND (PROD MIGZ)',
            'Lil Baby x Meek Mill x Quay Global Type Beat 2020 - Aretha (prod. GLAZER & noah)',
            'Kidd Keo Type Beat - Codeine (Prod. thatsauce)',
            'Mc Igu x Derek Type Beat - Pipe Prod. Johnny Lowd',
        ]);

        // 3) Club / Deep House (6)
        $attach($club, [
            'Элджей x Nebezao Deep House Club Trap Beat - Медуза Feduk Type Beat',
            'TRAVIS SCOTTFRVRFRIDAY TYPE BEAT FANTASIA',
            'GUITAR Poorstacy x Iann Dior Type Beat - Isolated (prod. perish)',
            'Iann Dior x Juice WRLD Pop Guitar Type Beat - Have Fun (prod. malloy x Aidan H)',
            'Speed Melodic Type Beat Trap Instrumental 2020',
            'Travis Scott Type Beat 2019 - Callejero Base de Trap Instrumental de Trap',
        ]);

        // 4) Trap Hits (6)
        $attach($trap, [
            'Lil Tjay x PnB Rock Type Beat - I\'ll Be There ft. Polo G',
            'Nav x The Weeknd Type Beat - i don\'t sleep at night',
            'Polo G x Lil Tjay Type Beat - Millions (prod Ramsey Beatz)',
            'Roddy Ricch x Mustard Type Beat "Fly Away"',
            'Travis Scott Type Beat 2019 - Callejero Base de Trap Instrumental de Trap',
            'Young Thug x Tory Lanez Type Beat - Otherside Trap Instrumental 2019',
        ]);

        // 5) Piano / Sad Pack (6)
        $attach($piano, [
            'Sad Piano Juice Wrld Type Beat \'Affection\'',
            'Sad XXXTentacion x Killstation Type Beat \'Plague\'',
            'Sad XXXTentacion x NF Piano Type Beat Relapse',
            'emo rap x lofi type beat - let me down',
            'AMARGUE - Deep Space Trap Type Beat - Trapani (Instrumental por KIDDKATZ)',
            'Lil Mosey x Lil Skies x Lil Tecca Type Beat - Cold Like (Prod. Snooza)',
        ]);
    }
}
