<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * NicknameGenerator Helpers
 */

namespace iceCMS2\Helpers;


class NicknameGenerator {
    private array $names = [
        'sr' => [
            'М' => ['Gvozdeni', 'Oluja', 'Senka', 'Vuk', 'Zmaj', 'Grom', 'Čelik', 'Lovac', 'Betmen', 'Spartanac', 'Testenina', 'Krompir', 'Mekdak', 'Palačinka', 'Kapetan', 'Mega', 'Super', 'Pizza', 'Sir', 'Kobasica', 'Mafin', 'Burrito', 'Krastavac', 'Tako', 'Guska', 'Pingvin', 'Burger', 'Turbo', 'Sneško', 'Brkati', 'Vafla', 'Krofna', 'Vladimir', 'Radovan', 'Miloš', 'Zlatko', 'Božidar', 'Nikola', 'Dušan', 'Aleksandar', 'Luka', 'Jovan'],
            'Ж' => ['Mesec', 'Zvezda', 'Kristal', 'Ruža', 'Vrana', 'Magla', 'Valkirija', 'Led', 'Čudo', 'Iskrica', 'Mačka', 'Banana', 'Trešnja', 'Krastavac', 'Olujna', 'Bundeva', 'Balončić', 'Kolačić', 'Princeza', 'Vila', 'Čokolada', 'Ananas', 'Jagoda', 'Med', 'Limun', 'Leptir', 'Sirena', 'Krofna', 'Pahuljica', 'Milica', 'Jelena', 'Dragana', 'Radmila', 'Bogdana', 'Teodora', 'Marija', 'Ana', 'Sara', 'Ivana']
        ],
        'ru' => [
            'М' => ['Боян', 'Рюрик', 'Пересвет', 'Всеволод', 'Добрыня', 'Гостомысл', 'Мстислав', 'Ратмир', 'Ярополк', 'Савва', 'Александр', 'Данила', 'Илья', 'Елисей', 'Тимур', 'Фёдор', 'Глеб', 'Ростислав', 'Святослав', 'Ладомир'],
            'Ж' => ['Аграфена', 'Пелагея', 'Феврония', 'Лукерья', 'Параскева', 'Милослава', 'Светозара', 'Евдокия', 'Ульяна', 'Дарина', 'Марфа', 'Стефания', 'Василиса', 'Злата', 'Любовь', 'Анастасия', 'Варвара', 'Ксения', 'Ярослава', 'Елена']
        ],
        'ge' => [
            'М' => ['თემურ', 'გიორგი', 'ბექა', 'ავთანდილ', 'ნოდარ', 'გელა', 'გურამ', 'ზვიად', 'შოთა', 'ტარიელ', 'ვახტანგ', 'თორნიკე', 'ირაკლი', 'მიხეილ', 'ნიკოლოზ', 'დავით', 'ოთარ', 'ლევან', 'ანდრია', 'სანდრო'],
            'Ж' => ['ნინო', 'ქეთევანი', 'თამარი', 'ანა', 'მარიამი', 'ელენე', 'ნატო', 'ლელა', 'სოფიო', 'დარეჯან', 'ნანა', 'ეკატერინე', 'მარინე', 'რუსუდან', 'ხათუნა', 'მზია', 'მანანა', 'თინათინ', 'თამუნა', 'მარი']
        ],
        'scandinavian' => [
            'М' => ['Bjorn', 'Ragnar', 'Harald', 'Leif', 'Thorstein', 'Sigurd', 'Ulf', 'Gunnar', 'Eirik', 'Knud', 'Olaf', 'Sven', 'Magnus', 'Hakon', 'Torben', 'Jorund', 'Freyr', 'Eldar', 'Halvar', 'Trygve'],
            'Ж' => ['Astrid', 'Sigrid', 'Ingrid', 'Freya', 'Thyra', 'Liv', 'Gunhild', 'Ragnhild', 'Helga', 'Gudrun', 'Eira', 'Sif', 'Yrsa', 'Hilda', 'Solveig', 'Brynhild', 'Alva', 'Marit', 'Tove', 'Vigdis']
        ],
        'orc' => [
            'М' => ['Gorgash', 'Throgg', 'Urgan', 'Drakthar', 'Grommash', 'Moktar', 'Rugor', 'Zugzug', 'Borash', 'Kargath', 'Durotan', 'Grashnak', 'Brakthar', 'Zogar', 'Urgash', 'Mornash', 'Torug', 'Thruk', 'Varnok', 'Kazgor'],
            'Ж' => ['Grasha', 'Drakha', 'Morga', 'Zulgra', 'Braksha', 'Ursha', 'Thragna', 'Greshka', 'Nagha', 'Vrokha', 'Zura', 'Yagra', 'Thurka', 'Magnar', 'Druza', 'Graksha', 'Lorzha', 'Vorza', 'Nargra', 'Zursha']
        ],
        'elf' => [
            'М' => ['Elrion', 'Faelar', 'Thalion', 'Eldrin', 'Aelor', 'Luthien', 'Caladwen', 'Varion', 'Sindar', 'Althion', 'Elandor', 'Thandor', 'Galadorn', 'Elorin', 'Arannis', 'Faenor', 'Quinlan', 'Lyari', 'Sylvar', 'Ilian'],
            'Ж' => ['Elara', 'Sylwen', 'Aerith', 'Lysara', 'Elenwe', 'Nimriel', 'Galadwen', 'Thalira', 'Faeliel', 'Isilwen', 'Althara', 'Yavanna', 'Maerwen', 'Thalindra', 'Seraphiel', 'Elvanna', 'Lyanna', 'Orlindra', 'Sylvara', 'Naeris']
        ],
        'alien' => [
            'М' => ['Xylox', 'Zentar', 'Quorax', 'Bliphax', 'Threx', 'Vorgal', 'Jxylos', 'Morgath', 'Xantor', 'Zyphar', 'Tzorak', 'Griblax', 'Yzlor', 'Vrilnax', 'Qephor', 'Zyron', 'Lotharax', 'Threxor', 'Zulkor', 'Vixlor'],
            'Ж' => ['Xylara', 'Quora', 'Zyphara', 'Threxia', 'Blipha', 'Norgala', 'Vorgana', 'Jxyla', 'Zintara', 'Morgatha', 'Vixira', 'Tzora', 'Quintra', 'Lyzora', 'Thralis', 'Nyzira', 'Yzora', 'Xantara', 'Jylxia', 'Vrixa']
        ],
        'dwarf' => [
            'М' => ['Thorin', 'Balin', 'Dain', 'Gimli', 'Durin', 'Bofur', 'Dwalin', 'Oin', 'Gloin', 'Borin', 'Thrain', 'Frerin', 'Bombur', 'Narvi', 'Fundin'],
            'Ж' => ['Dis', 'Hilda', 'Bruni', 'Gerta', 'Thora', 'Brynna', 'Durla', 'Gilda', 'Mira', 'Olga', 'Frida', 'Edda', 'Yrsa']
        ],
        'zoomer' => [
            'М' => ['CringeLord', 'NFT_Dealer', 'TikTokKing', '420BlazeIt', 'Sigma', 'FortnitePro', 'SusMaster', 'Ligma', 'BasedBoi'],
            'Ж' => ['QueenBee', 'VSCO_Girl', 'BobaAddict', 'EgirlSupreme', 'Softie', 'UwUChan', 'TikTokStar', 'AltVibes'],
            'прочее' => ['Yeet', 'ChadVibes', 'NoCap', 'ZoomerLord', 'Bussin', 'SussyBaka', 'Rizzler', 'ViralVibes']
        ],
        'star_wars' => [
            'М' => ['Darth Rengor', 'Jax Vandar', 'Obran Stark', 'Kylo Tark', 'Marek Windu', 'Garron Tano',
                    'Threx Dooku', 'Zan Skyblade', 'Torin Organa', 'Vos Kenobi', 'Rek Sunrider', 'Bast Voss',
                    'Tyrus Palpatine', 'Jor Drax', 'Xan Thul'],
            'Ж' => ['Lara Amidala', 'Jyn Valeria', 'Ahsana Tano', 'Reeva Syndulla', 'Sabine Vex', 'Mira Kenobi',
                    'Briya Skywalker', 'Zara Ventress', 'Talia Dooku', 'Senya Organa', 'Yara Satele', 'Risha Wren',
                    'Aria Calrissian', 'Nova Sunrider', 'Siri Vos']
        ],
        'star_trek' => [
            'М' => ['Spock', 'Jameson Kirk', 'T’Lan Surak', 'Worf Thak', 'Zarek Picard', 'Quint Riker',
                    'Dax Nog', 'Garak Lor', 'Elim Sisko', 'Tuvok Rom', 'Kurn Damar', 'Miro Chakotay',
                    'Shon T’Pol', 'Orin LaForge', 'Threx Archer'],
            'Ж' => ['T’Lana Surak', 'Nyota Uhura', 'Jadzia Dax', 'Seven of Nine', 'Kira Nerys', 'Ro Laren',
                    'Ezri Tigan', 'Deanna Troi', 'B’Elanna Torres', 'Saavik', 'Ilia Vex', 'T’Pring',
                    'Miral Torres', 'Kasidy Yates', 'Sylva Sato']
        ],
    ];

    private array $suffixes = [
        'sr' => ['Vitez', 'Jahač', 'Ratnik', 'Mag', 'Čuvar', 'Ubica', 'Oluja', 'Meknaget', 'Majstor', 'Burrito', 'Testenina', 'Plesač', 'Igrač', 'Legenda', 'Hotdog', 'Čubaka', 'Jeti', 'Nindža', 'Istraživač'],
        'ru' => ['Рыцарь', 'Всадник', 'Воин', 'Маг', 'Страж', 'Убийца', 'Гроза', 'Макнаггет', 'Мастер', 'Буррито', 'Лапша', 'Чих', 'Танцор', 'Легенда', 'Хотдог', 'Чубака', 'Йети', 'Ниндзя', 'Исследователь'],
        'ge' => ['რაინდი', 'მხედარი', 'მებრძოლი', 'მაგი', 'მცველი', 'მკვლელი', 'ქარიშხალი', 'მაკნაგეთი', 'ოსტატი', 'ბურიტო', 'ლაფშა', 'ცხვირი', 'მოცეკვავე', 'ლეგენდა', 'ჰოთდოგი', 'ჩუბაკა', 'იეტი', 'ნინძა', 'აღმომჩენი'],
        'scandinavian' => ['Berserker', 'Jarl', 'Skald', 'Viking', 'Drengr', 'Seer', 'Shieldmaiden', 'Runemaster', 'Thor’s Chosen', 'Saga Teller'],
        'orc' => ['Bloodfist', 'Skullcrusher', 'Ironjaw', 'Bonechewer', 'Doomhammer', 'Warbringer', 'Gorefang', 'Stormfury', 'Darkhowl', 'Rageclaw'],
        'elf' => ['Moonblade', 'Silverleaf', 'Stardancer', 'Dawnwhisper', 'Windrider', 'Lightbringer', 'Starborn', 'Elderwood', 'Dreamwalker', 'Sunfire'],
        'alien' => ['Xenowalker', 'Starborn', 'Voidseeker', 'Nebulashard', 'Photonflare', 'Starlancer', 'Galaxian', 'Exoform', 'Neuroflux', 'Quasari'],
        'dwarf' => [
            'Goldbeard', 'Ironfist', 'Stonehelm', 'Hammerhand', 'Deepdelver', 'Rockbreaker',
            'Blackhammer', 'Strongarm', 'Fireforge', 'Oakenshield', 'Runecarver', 'Silvervein',
            'Mithrilborn', 'Anvilhand', 'Bronzebeard', 'Stonefury', 'Forgekeeper', 'Ironfoot',
            'Thunderaxe', 'Redbeard'
        ],
        'zoomer' => [
            'The Cringelord', 'NoCap', 'Based', 'Bussin', 'The Rizzler', 'Viral', 'TikTokStar',
            'Sigma', 'Gamer', '420', 'NFT_Dealer', 'ZoomerKing', 'SussyBaka', 'YeetMaster',
            'FortnitePro', 'DripGod', 'UwU', 'SimpSlayer', 'BigMood', 'LigmaLegend'
        ],
        'star_wars' => [
            'the Sith Lord', 'Jedi Master', 'Dark Side Seeker', 'Bounty Hunter', 'Sith Apprentice',
            'Mandalorian Warrior', 'Force Wielder', 'Galactic Smuggler', 'Droid Commander',
            'TIE Fighter Pilot', 'Rebel Leader', 'Clone Trooper', 'Imperial Officer', 'Jedi Knight',
            'Sith Assassin'
        ],
        'star_trek' => [
            'of the Federation', 'Starfleet Captain', 'Vulcan Strategist', 'Klingon Warrior',
            'Chief Engineer', 'Borg Assimilated', 'Deep Space Explorer', 'Temporal Operative',
            'Galaxy Traveler', 'Science Officer', 'Warp Core Specialist', 'Starfleet Admiral',
            'Tactical Officer', 'Holodeck Master', 'Romulan Spy'
        ],
    ];

    public function generate(string $gender = null, string $lang = null): string {
        if (!$lang) {
            $lang = $this->randomElement(array_keys($this->names));
        }

        if (!$gender) {
            $gender = $this->randomElement(['М', 'Ж']);
        }

        if (!isset($this->names[$lang][$gender])) {
            $gender = 'прочее';
        }

        $name1 = $this->randomElement($this->names[$lang][$gender]);
        $name2 = $this->randomElement($this->suffixes[$lang] ?? $this->suffixes['sr']);

        return "$name1 $name2";
    }

    private function randomElement(array $array): string {
        return $array[array_rand($array)];
    }
}