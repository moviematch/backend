<?php

namespace Database\Seeders;

use App\Models\Word;
use Illuminate\Database\Seeder;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Str;

class WordSeeder extends Seeder
{

    /** Words to ban */
    protected const illegal = [
        'meat',
        'problem',
        'army',
        'failure',
        'disease',
        'cigarette',
        'death',
        'blood',
        'criticism',
        'debt',
        'alcohol',
        'complaint',
        'steak',
        'cancer',
        'tension',
        'anxiety',
        'disaster',
        'church',
        'error',
        'injury',
        'garbage',
        'honey',
        'funeral',
        'surgery',
        'two',
        'health',
        'food',
        'marriage',
        'wood',
        'performance',
        'depression',
        'able',
        'pregnant',
        'successfully',
        'sexual',
        'like',
        'moaning',
        'racial',
        'macho',
        'kindly',
        'sedate',
        'dead',
        'erect',
        'three',
        'lewd',
        'creepy',
        'meaty',
        'six',
        'nutty',
        'obese',
        'nine',
        'milky',
        'one',
        'handsomely'
    ];

    /** Words to add if not there yet */
    protected const ensure = [
        'shrimp',
        'prawn',
        ['shrimpy', 'adj'],
        ['prawny', 'adj'],
        'lobster',
        'frog',
        'rice',
        'lentil',
        'tree',
        ['plant-based', 'adj'],
        'plant',
        'coral'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        Word::truncate();

        $client = new GuzzleClient(['verify' => false]);

        // fetch list of nouns
        $response = $client->get('http://randomwordgenerator.com/json/nouns_ws.json');
        if($response->getStatusCode() != 200){
            throw new \Exception('Cannot access list of nouns');
        }
        $list = json_decode($response->getBody())->data;
        foreach($list as $word){
            $word = $word->noun->value;
            if(Str::endsWith($word, ['ing', 's'])) continue; // ignore "-ing" and plural words
            if(in_array(strtolower($word), self::illegal)) continue; // ignore blacklisted words
            Word::create([
                'word' => $word,
                'adjective' => 0
            ]);
        }

        // fetch list of nouns
        $response = $client->get('http://randomwordgenerator.com/json/adjectives_ws.json');
        if($response->getStatusCode() != 200){
            throw new \Exception('Cannot access list of adjectives');
        }
        $list = json_decode($response->getBody())->data;
        foreach($list as $word){
            $word = $word->adjective->value;
            if(in_array(strtolower($word), self::illegal)) continue; // ignore blacklisted words
            Word::create([
                'word' => $word,
                'adjective' => 1
            ]);
        }

        // ensure words in `ensure` list are added
        foreach(self::ensure as $ensured){
            $word = is_array($ensured) ? $ensured[0] : $ensured;
            $adjective = is_array($ensured) && isset($ensured[1]) && $ensured[1] == 'adj' ? 1 : 0;
            $entry = Word::where('word', $word)->where('adjective', $adjective)->first();
            if(!$entry){
                Word::create([
                    'word' => $word,
                    'adjective' => $adjective
                ]);
            }
        }

    }
}
