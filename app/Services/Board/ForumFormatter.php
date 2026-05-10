<?php

namespace App\Services\Board;

use Illuminate\Support\Facades\File;
use s9e\TextFormatter\Configurator;
use s9e\TextFormatter\Parser;
use s9e\TextFormatter\Renderer;

class ForumFormatter
{
    private static ?Parser $parser = null;

    private static ?Renderer $renderer = null;

    /**
     * @var array<string, array{0: string, 1: string}>
     */
    private const EMOTICONS = [
        ':))' => ['31.gif', 'glücklich lächelnd'],
        ':)' => ['30.gif', 'lächelnd'],
        ':]' => ['32.gif', 'selbstzufrieden lächelnd'],
        ':D' => ['33.gif', 'lachend'],
        ':rofl:' => ['34.gif', 'auf dem Boden rollend vor Lachen'],
        ';)' => ['35.gif', 'zwinkernd'],
        ':(' => ['36.gif', 'traurig'],
        ';o' => ['37.gif', 'erschöpft'],
        ';(' => ['38.gif', 'weinend'],
        'X(' => ['39.gif', 'wütend'],
        'X/' => ['40.gif', 'fuchsteufelswild'],
        ':teufel:' => ['41.gif', 'teuflisch'],
        ':o' => ['54.gif', 'geplagt'],
        '?(' => ['55.gif', 'fragend'],
        '8o' => ['56.gif', 'schockiert'],
        '8)' => ['57.gif', 'cool'],
        ':P' => ['58.gif', 'die Zunge rausstreckend'],
        ':tongue:' => ['59.gif', 'fröhlich lachend'],
        ':rolleyes:' => ['63.gif', 'augenrollend'],
        ':baby:' => ['64.gif', 'Baby'],
        ':hallo:' => ['65.gif', ''],
        ':genial:' => ['66.gif', ''],
        '<3' => ['67.gif', ''],
        ':>' => ['69.gif', ''],
        ':3' => ['72.gif', ''],
        ':adel:' => ['42.gif', ''],
        ':koenig:' => ['43.gif', ''],
        ':kaiser:' => ['44.gif', ''],
        ':veuxin:' => ['45.gif', ''],
        ':trinken' => ['46.gif', ''],
        ':party:' => ['47.gif', ''],
        ':ritter:' => ['48.gif', ''],
        ':angriff:' => ['49.gif', ''],
        ':fechter:' => ['50.gif', ''],
        ':spam:' => ['51.gif', ''],
        ':dito:' => ['52.gif', ''],
        ':urlaub:' => ['53.gif', ''],
        ':richter:' => ['60.gif', ''],
        ':hero:' => ['61.gif', ''],
        ':ritter2:' => ['62.gif', ''],
        ':tinos:' => ['68.gif', ''],
        ':met:' => ['70.gif', ''],
        '=)' => ['31.gif', ''],
        ':evil:' => ['40.gif', ''],
        ':birth:' => ['47.gif', ''],
        ':cool:' => ['57.gif', ''],
        ':verliebt:' => ['67.gif', ''],
    ];

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function emoticons(): array
    {
        return self::EMOTICONS;
    }

    public function render(?string $text, bool $smilies = true): string
    {
        $html = self::renderer()->render(self::parser()->parse($this->applyLegacyTypographyCompatibility((string) $text)));
        $html = nl2br($html, false);

        return $smilies ? $this->replaceEmoticons($html) : $html;
    }

    private static function parser(): Parser
    {
        self::boot();

        return self::$parser;
    }

    private static function renderer(): Renderer
    {
        self::boot();

        return self::$renderer;
    }

    private static function boot(): void
    {
        if (self::$parser && self::$renderer) {
            return;
        }

        $configurator = new Configurator();
        $configurator->rendering->setEngine('PHP', self::cacheDirectory());
        $configurator->FancyPants->disablePass('Quotes');
        $configurator->FancyPants->disablePass('Guillemets');
        $configurator->FancyPants->disablePass('MathSymbols');
        $configurator->FancyPants->disablePass('Symbols');

        foreach (['B', 'I', 'U', 'URL', 'IMG', 'CODE', 'LIST', '*', 'UL'] as $bbcode) {
            $configurator->BBCodes->addFromRepository($bbcode);
        }

        $configurator->BBCodes->addCustom('[LI]{TEXT}[/LI]', '<li>{TEXT}</li>');
        $configurator->BBCodes->addCustom('[COLOR={COLOR}]{TEXT}[/COLOR]', '<span style="color:{COLOR}">{TEXT}</span>');
        $configurator->BBCodes->addCustom('[SIZE={UINT}]{TEXT}[/SIZE]', '<font size="{UINT}">{TEXT}</font>');

        $configurator->BBCodes->addCustom('[H]{TEXT}[/H]', '<div class="action post">{TEXT}</div>');
        $configurator->BBCodes->addCustom('[HANDLUNG]{TEXT}[/HANDLUNG]', '<div class="action post">{TEXT}</div>');
        $configurator->BBCodes->addCustom('[S]{TEXT}[/S]', '<div class="simoff post">{TEXT}</div>');
        $configurator->BBCodes->addCustom('[SIMOFF]{TEXT}[/SIMOFF]', '<div class="simoff post">{TEXT}</div>');
        $configurator->BBCodes->addCustom('[SL]{TEXT}[/SL]', '<div class="sl post">{TEXT}</div>');

        $configurator->BBCodes->addCustom('[Q]{TEXT}[/Q]', '<blockquote>{TEXT}</blockquote>');
        $configurator->BBCodes->addCustom('[Q={TEXT1}]{TEXT2}[/Q]', '<blockquote><cite>{TEXT1}</cite>{TEXT2}</blockquote>');
        $configurator->BBCodes->addCustom('[QUOTE]{TEXT}[/QUOTE]', '<blockquote>{TEXT}</blockquote>');
        $configurator->BBCodes->addCustom('[QUOTE={TEXT1}]{TEXT2}[/QUOTE]', '<blockquote><cite>{TEXT1}</cite>{TEXT2}</blockquote>');

        $configurator->BBCodes->addCustom('[LEFT]{TEXT}[/LEFT]', '<p style="text-align:left;">{TEXT}</p>');
        $configurator->BBCodes->addCustom('[CENTER]{TEXT}[/CENTER]', '<p style="text-align:center;">{TEXT}</p>');
        $configurator->BBCodes->addCustom('[RIGHT]{TEXT}[/RIGHT]', '<p style="text-align:right;">{TEXT}</p>');

        $formatter = $configurator->finalize();

        self::$parser = $formatter['parser'];
        self::$renderer = $formatter['renderer'];
    }

    private static function cacheDirectory(): string
    {
        $directory = storage_path('framework/cache/textformatter');

        File::ensureDirectoryExists($directory);

        return $directory;
    }

    private function applyLegacyTypographyCompatibility(string $html): string
    {
        $html = preg_replace('/\s-\s/u', ' – ', $html);

        return preg_replace('/"([^"\n]+)"/u', '»$1«', $html);
    }

    private function replaceEmoticons(string $html): string
    {
        foreach (self::EMOTICONS as $code => [$file, $title]) {
            $escapedCode = htmlspecialchars($code, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $html = str_replace($escapedCode, $this->emoticonImage($file, $title, $code), $html);
        }

        return $html;
    }

    private function emoticonImage(string $file, string $title, string $code): string
    {
        $titleAttribute = e($title);
        $altAttribute = e($code);

        return '<img src="' . asset('images/emoticon/' . $file) . '" title="' . $titleAttribute . '" alt="' . $altAttribute . '">';
    }
}
