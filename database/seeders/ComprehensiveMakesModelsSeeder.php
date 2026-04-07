<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ComprehensiveMakesModelsSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks for clean seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        VehicleModel::truncate();
        VehicleMake::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->seedCarMakes();
        $this->seedMotorcycleMakes();
        $this->seedTruckMakes();
        $this->seedVanMakes();
        $this->seedCaravanMakes();
        $this->seedTrailerMakes();
        $this->seedConstructionMakes();
        $this->seedAgriculturalMakes();
        $this->seedForkliftMakes();
        $this->seedBusMakes();
    }

    private function createMakeWithModels(string $name, string $type, array $models = [], ?string $logo = null): void
    {
        $make = VehicleMake::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'type' => $type,
            'logo' => $logo,
        ]);

        foreach ($models as $modelName) {
            VehicleModel::create([
                'make_id' => $make->id,
                'name' => $modelName,
                'slug' => Str::slug($modelName),
            ]);
        }
    }

    private function seedCarMakes(): void
    {
        // Based on AutoScout24.com real makes and models
        $carMakes = [
            'Abarth' => ['124 Spider', '500', '500C', '500e', '595', '595C', '695', 'Punto'],
            'Aiways' => ['U5', 'U6'],
            'Alfa Romeo' => ['147', '156', '159', '166', '4C', 'Giulia', 'Giulietta', 'MiTo', 'Stelvio', 'Tonale'],
            'Alpine' => ['A110', 'A290'],
            'Aston Martin' => ['Cygnet', 'DB11', 'DB9', 'DBX', 'Rapide', 'V8 Vantage', 'Vanquish', 'Vantage'],
            'Audi' => ['A1', 'A2', 'A3', 'A4', 'A4 Allroad', 'A5', 'A6', 'A6 Allroad', 'A7', 'A8', 'e-tron', 'e-tron GT', 'Q2', 'Q3', 'Q4 e-tron', 'Q5', 'Q7', 'Q8', 'Q8 e-tron', 'R8', 'RS3', 'RS4', 'RS5', 'RS6', 'RS7', 'RS Q3', 'RS Q8', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8', 'SQ5', 'SQ7', 'SQ8', 'TT', 'TTS'],
            'Bentley' => ['Bentayga', 'Continental', 'Flying Spur', 'Mulsanne'],
            'BMW' => ['1 Series', '2 Series', '2 Series Active Tourer', '2 Series Gran Coupe', '3 Series', '4 Series', '5 Series', '6 Series', '7 Series', '8 Series', 'i3', 'i4', 'i5', 'i7', 'iX', 'iX1', 'iX2', 'iX3', 'M2', 'M3', 'M4', 'M5', 'M8', 'X1', 'X2', 'X3', 'X4', 'X5', 'X6', 'X7', 'XM', 'Z4'],
            'Bugatti' => ['Chiron', 'Veyron'],
            'BYD' => ['Atto 3', 'Dolphin', 'Han', 'Seal', 'Seal U', 'Sealion 7', 'Tang'],
            'Cadillac' => ['CT5', 'Escalade', 'XT4', 'XT5', 'XT6'],
            'Chevrolet' => ['Camaro', 'Corvette', 'Cruze', 'Equinox', 'Malibu', 'Spark', 'Tahoe', 'Trax'],
            'Chrysler' => ['300C', 'Grand Voyager', 'Pacifica', 'PT Cruiser', 'Voyager'],
            'Citroen' => ['Berlingo', 'C1', 'C3', 'C3 Aircross', 'C4', 'C4 Cactus', 'C4 Picasso', 'C5', 'C5 Aircross', 'C5 X', 'DS3', 'DS4', 'DS5', 'Grand C4 Picasso', 'Jumpy', 'SpaceTourer', 'e-Berlingo', 'e-C4'],
            'CUPRA' => ['Ateca', 'Born', 'Formentor', 'Leon', 'Tavascan', 'Terramar'],
            'Dacia' => ['Dokker', 'Duster', 'Jogger', 'Logan', 'Lodgy', 'Sandero', 'Spring'],
            'Daewoo' => ['Kalos', 'Lacetti', 'Matiz', 'Nubira'],
            'Daihatsu' => ['Copen', 'Cuore', 'Materia', 'Sirion', 'Terios', 'YRV'],
            'DS' => ['DS 3', 'DS 3 Crossback', 'DS 4', 'DS 7', 'DS 7 Crossback', 'DS 9'],
            'Ferrari' => ['296 GTB', '488', '812', 'California', 'F8', 'GTC4Lusso', 'Portofino', 'Purosangue', 'Roma', 'SF90'],
            'Fiat' => ['124 Spider', '500', '500C', '500e', '500L', '500X', '600', 'Bravo', 'Doblo', 'Ducato', 'Fiorino', 'Grande Punto', 'Panda', 'Punto', 'Qubo', 'Tipo'],
            'Ford' => ['B-Max', 'C-Max', 'EcoSport', 'Edge', 'Explorer', 'Fiesta', 'Focus', 'Galaxy', 'Ka+', 'Kuga', 'Mondeo', 'Mustang', 'Mustang Mach-E', 'Puma', 'Ranger', 'S-Max', 'Tourneo Connect', 'Tourneo Custom', 'Transit Connect'],
            'Genesis' => ['G70', 'G80', 'GV60', 'GV70', 'GV80'],
            'Honda' => ['Civic', 'CR-V', 'e', 'e:Ny1', 'HR-V', 'Jazz', 'ZR-V'],
            'Hyundai' => ['Bayon', 'i10', 'i20', 'i30', 'i40', 'IONIQ', 'IONIQ 5', 'IONIQ 6', 'Kona', 'Nexo', 'Santa Fe', 'Staria', 'Tucson'],
            'Infiniti' => ['FX', 'G', 'Q30', 'Q50', 'Q70', 'QX30', 'QX50', 'QX70'],
            'Isuzu' => ['D-Max'],
            'Jaguar' => ['E-Pace', 'F-Pace', 'F-Type', 'I-Pace', 'XE', 'XF', 'XJ'],
            'Jeep' => ['Avenger', 'Cherokee', 'Compass', 'Commander', 'Grand Cherokee', 'Renegade', 'Wrangler'],
            'Kia' => ['Ceed', 'EV6', 'EV9', 'Niro', 'Optima', 'Picanto', 'Pro Ceed', 'Rio', 'Sorento', 'Soul', 'Sportage', 'Stinger', 'Stonic', 'Venga', 'XCeed'],
            'Lamborghini' => ['Aventador', 'Huracan', 'Revuelto', 'Urus'],
            'Lancia' => ['Delta', 'Musa', 'Ypsilon'],
            'Land Rover' => ['Defender', 'Discovery', 'Discovery Sport', 'Freelander', 'Range Rover', 'Range Rover Evoque', 'Range Rover Sport', 'Range Rover Velar'],
            'Lexus' => ['CT', 'ES', 'GS', 'IS', 'LC', 'LS', 'LX', 'NX', 'RC', 'RX', 'RZ', 'UX'],
            'Lincoln' => ['Aviator', 'Continental', 'Navigator'],
            'Lotus' => ['Eletre', 'Elise', 'Emira', 'Evora', 'Exige'],
            'Maserati' => ['Ghibli', 'GranCabrio', 'GranTurismo', 'Grecale', 'Levante', 'MC20', 'Quattroporte'],
            'Mazda' => ['2', '3', '5', '6', 'CX-3', 'CX-30', 'CX-5', 'CX-60', 'MX-30', 'MX-5'],
            'McLaren' => ['540C', '570S', '600LT', '720S', '750S', 'Artura', 'GT'],
            'Mercedes-Benz' => ['A-Class', 'AMG GT', 'B-Class', 'C-Class', 'CLA', 'CLE', 'CLS', 'E-Class', 'EQA', 'EQB', 'EQC', 'EQE', 'EQE SUV', 'EQS', 'EQS SUV', 'EQV', 'G-Class', 'GLA', 'GLB', 'GLC', 'GLC Coupe', 'GLE', 'GLE Coupe', 'GLS', 'Marco Polo', 'S-Class', 'SL', 'SLC', 'Sprinter', 'T-Class', 'V-Class', 'Vito'],
            'MG' => ['4', '5', 'EHS', 'HS', 'Marvel R', 'MG3', 'MG4', 'MG5', 'ZS', 'ZS EV'],
            'MINI' => ['Clubman', 'Convertible', 'Countryman', 'Coupe', 'Hatch', 'Paceman'],
            'Mitsubishi' => ['ASX', 'Eclipse Cross', 'L200', 'Outlander', 'Space Star'],
            'Nissan' => ['Ariya', 'Juke', 'Leaf', 'Micra', 'Navara', 'Note', 'Pathfinder', 'Primastar', 'Pulsar', 'Qashqai', 'Townstar', 'X-Trail'],
            'Opel' => ['Adam', 'Astra', 'Combo', 'Corsa', 'Crossland', 'Crossland X', 'Grandland', 'Grandland X', 'Insignia', 'Karl', 'Meriva', 'Mokka', 'Mokka-e', 'Vivaro', 'Zafira', 'Zafira Life'],
            'Peugeot' => ['108', '2008', '208', '3008', '308', '408', '5008', '508', '508 SW', 'Partner', 'Rifter', 'Traveller', 'e-2008', 'e-208', 'e-308'],
            'Polestar' => ['1', '2', '3', '4'],
            'Porsche' => ['718 Boxster', '718 Cayman', '911', 'Cayenne', 'Macan', 'Panamera', 'Taycan'],
            'Renault' => ['Arkana', 'Austral', 'Captur', 'Clio', 'Espace', 'Kadjar', 'Kangoo', 'Koleos', 'Master', 'Megane', 'Megane E-Tech', 'Rafale', 'Scenic', 'Scenic E-Tech', 'Talisman', 'Twingo', 'Twizy', 'Zoe'],
            'Rolls-Royce' => ['Cullinan', 'Dawn', 'Ghost', 'Phantom', 'Silver Shadow', 'Spectre', 'Wraith'],
            'Saab' => ['9-3', '9-5'],
            'SEAT' => ['Alhambra', 'Arona', 'Ateca', 'Ibiza', 'Leon', 'Mii', 'Tarraco', 'Toledo'],
            'Skoda' => ['Enyaq', 'Fabia', 'Kamiq', 'Karoq', 'Kodiaq', 'Octavia', 'Rapid', 'Roomster', 'Scala', 'Superb', 'Yeti'],
            'Smart' => ['#1', '#3', 'EQ fortwo', 'forfour', 'fortwo'],
            'SsangYong' => ['Korando', 'Rexton', 'Tivoli', 'Torres'],
            'Subaru' => ['BRZ', 'Crosstrek', 'Forester', 'Impreza', 'Legacy', 'Levorg', 'Outback', 'Solterra', 'WRX', 'XV'],
            'Suzuki' => ['Across', 'Alto', 'Baleno', 'Celerio', 'Ignis', 'Jimny', 'S-Cross', 'SX4 S-Cross', 'Swift', 'Swace', 'Vitara'],
            'Tesla' => ['Model 3', 'Model S', 'Model X', 'Model Y', 'Cybertruck'],
            'Toyota' => ['Aygo', 'Aygo X', 'bZ4X', 'C-HR', 'Camry', 'Corolla', 'GR86', 'GR Supra', 'GR Yaris', 'Highlander', 'Hilux', 'Land Cruiser', 'Mirai', 'Proace City', 'Proace Verso', 'RAV4', 'Supra', 'Yaris', 'Yaris Cross'],
            'Volkswagen' => ['Arteon', 'Atlas', 'Caddy', 'California', 'Caravelle', 'e-Golf', 'Golf', 'ID.3', 'ID.4', 'ID.5', 'ID.7', 'ID. Buzz', 'Jetta', 'Multivan', 'Passat', 'Polo', 'Sharan', 'T-Cross', 'T-Roc', 'Taigo', 'Tiguan', 'Tiguan Allspace', 'Touareg', 'Touran', 'Transporter', 'Up!'],
            'Volvo' => ['C30', 'C40', 'C70', 'EX30', 'EX40', 'EX90', 'S40', 'S60', 'S90', 'V40', 'V60', 'V60 Cross Country', 'V90', 'V90 Cross Country', 'XC40', 'XC60', 'XC90'],
            'XPeng' => ['G6', 'G9', 'P5', 'P7'],
            'Other' => [],
        ];

        foreach ($carMakes as $makeName => $models) {
            $this->createMakeWithModels($makeName, 'car', $models);
        }
    }

    private function seedMotorcycleMakes(): void
    {
        $motorcycleMakes = [
            'Aprilia' => ['Dorsoduro', 'RS 125', 'RS 660', 'RSV4', 'Shiver', 'SR GT', 'Tuareg 660', 'Tuono'],
            'Benelli' => ['502C', 'Leoncino', 'TRK 502', 'TRK 702'],
            'BMW' => ['C 400', 'CE 04', 'F 750 GS', 'F 800 GS', 'F 850 GS', 'F 900 R', 'F 900 XR', 'G 310 GS', 'G 310 R', 'K 1600', 'M 1000 R', 'M 1000 RR', 'R 1250 GS', 'R 1250 RS', 'R 1250 RT', 'R 1300 GS', 'R NineT', 'R18', 'S 1000 R', 'S 1000 RR', 'S 1000 XR'],
            'Ducati' => ['Diavel', 'DesertX', 'Hypermotard', 'Monster', 'Multistrada', 'Panigale V2', 'Panigale V4', 'Scrambler', 'Streetfighter V2', 'Streetfighter V4', 'SuperSport'],
            'Harley-Davidson' => ['Breakout', 'CVO', 'Electra Glide', 'Fat Bob', 'Fat Boy', 'Heritage', 'Iron 883', 'LiveWire', 'Low Rider', 'Nightster', 'Pan America', 'Road Glide', 'Road King', 'Softail', 'Sportster', 'Street Bob', 'Street Glide', 'Touring', 'Ultra Limited'],
            'Honda' => ['Africa Twin', 'CB 125R', 'CB 500F', 'CB 500X', 'CB 650R', 'CB 1000R', 'CBR 500R', 'CBR 600RR', 'CBR 650R', 'CBR 1000RR', 'CRF 1100L', 'Forza', 'Gold Wing', 'Monkey', 'NC 750X', 'NT1100', 'PCX', 'Rebel', 'SH', 'X-ADV'],
            'Husqvarna' => ['701 Enduro', '701 Supermoto', 'Norden 901', 'Svartpilen', 'Vitpilen'],
            'Indian' => ['Challenger', 'Chief', 'FTR', 'Scout', 'Springfield'],
            'Kawasaki' => ['ER-6n', 'Ninja 400', 'Ninja 650', 'Ninja H2', 'Ninja ZX-6R', 'Ninja ZX-10R', 'Versys 650', 'Versys 1000', 'Vulcan', 'W800', 'Z400', 'Z650', 'Z900', 'Z H2', 'ZZR 1400'],
            'KTM' => ['125 Duke', '200 Duke', '390 Adventure', '390 Duke', '690 Enduro', '690 SMC', '790 Adventure', '790 Duke', '890 Adventure', '890 Duke', '1290 Super Adventure', '1290 Super Duke', 'RC 125', 'RC 390'],
            'Moto Guzzi' => ['California', 'Griso', 'Mandello', 'Stelvio', 'V7', 'V85 TT', 'V100 Mandello'],
            'MV Agusta' => ['Brutale', 'Dragster', 'F3', 'Rush', 'Superveloce', 'Turismo Veloce'],
            'Royal Enfield' => ['Classic 350', 'Continental GT', 'Himalayan', 'Hunter 350', 'Interceptor', 'Meteor', 'Super Meteor'],
            'Suzuki' => ['Bandit', 'DL 650 V-Strom', 'DL 1050 V-Strom', 'GSX-R 600', 'GSX-R 750', 'GSX-R 1000', 'GSX-S 750', 'GSX-S 1000', 'Hayabusa', 'Katana', 'SV 650'],
            'Triumph' => ['Bonneville', 'Daytona', 'Explorer', 'Rocket III', 'Scrambler', 'Speed Triple', 'Speed Twin', 'Street Triple', 'Thruxton', 'Tiger 660', 'Tiger 800', 'Tiger 900', 'Tiger 1200', 'Trident', 'Trophy'],
            'Yamaha' => ['FJR 1300', 'MT-03', 'MT-07', 'MT-09', 'MT-10', 'NMAX', 'R1', 'R125', 'R3', 'R6', 'R7', 'T-Max', 'Tenere 700', 'Tracer 7', 'Tracer 9', 'Tracer 900', 'X-Max', 'XSR 700', 'XSR 900', 'YZF-R1', 'YZF-R125'],
            'CFMOTO' => ['300NK', '650GT', '650NK', '700CL-X', '800MT'],
            'Vespa' => ['GTS', 'Primavera', 'Sprint'],
            'Piaggio' => ['Beverly', 'Liberty', 'Medley', 'MP3'],
            'Other' => [],
        ];

        foreach ($motorcycleMakes as $makeName => $models) {
            $this->createMakeWithModels($makeName, 'motorcycle', $models);
        }
    }

    private function seedTruckMakes(): void
    {
        // Based on TruckScout24.com - trucks over 7.5t
        $truckMakes = [
            'DAF' => ['CF', 'LF', 'XD', 'XF', 'XG', 'XG+'],
            'Iveco' => ['Daily', 'Eurocargo', 'S-Way', 'Stralis', 'Trakker', 'X-Way'],
            'MAN' => ['TGA', 'TGE', 'TGL', 'TGM', 'TGS', 'TGX'],
            'Mercedes-Benz' => ['Actros', 'Antos', 'Arocs', 'Atego', 'Axor', 'eActros', 'Econic', 'Zetros'],
            'Renault' => ['C', 'D', 'D Wide', 'K', 'Master', 'T', 'T High'],
            'Scania' => ['G', 'L', 'P', 'R', 'S'],
            'Volvo' => ['FE', 'FH', 'FH16', 'FL', 'FM', 'FMX'],
            'Ford' => ['Cargo', 'F-Max'],
            'Kenworth' => ['T680', 'T880', 'W900'],
            'Peterbilt' => ['389', '579', '567'],
            'Freightliner' => ['Cascadia', 'M2', 'Columbia'],
            'Mack' => ['Anthem', 'Granite', 'Pinnacle'],
            'KAMAZ' => ['5490', '6520', '65115'],
            'Tatra' => ['Phoenix', 'Terra'],
            'Isuzu' => ['Forward', 'N-Series'],
            'Mitsubishi Fuso' => ['Canter', 'Super Great'],
            'Hino' => ['300', '500', '700'],
            'Other' => [],
        ];

        foreach ($truckMakes as $makeName => $models) {
            $this->createMakeWithModels($makeName, 'truck', $models);
        }
    }

    private function seedVanMakes(): void
    {
        // Vans & trucks up to 7.5t from TruckScout24 + AutoScout24
        $vanMakes = [
            'Citroen' => ['Berlingo', 'Jumper', 'Jumpy', 'SpaceTourer'],
            'Fiat' => ['Doblo', 'Ducato', 'Fiorino', 'Scudo', 'Talento'],
            'Ford' => ['Transit', 'Transit Connect', 'Transit Courier', 'Transit Custom', 'Tourneo', 'Tourneo Connect', 'Tourneo Custom', 'Ranger'],
            'Iveco' => ['Daily'],
            'MAN' => ['TGE'],
            'Maxus' => ['Deliver 9', 'eDeliver 3', 'eDeliver 9', 'T60'],
            'Mercedes-Benz' => ['Citan', 'eSprinter', 'eVito', 'Sprinter', 'V-Class', 'Vito'],
            'Nissan' => ['Interstar', 'NV200', 'NV300', 'NV400', 'Primastar', 'Townstar'],
            'Opel' => ['Combo', 'Movano', 'Vivaro', 'Zafira Life'],
            'Peugeot' => ['Boxer', 'Expert', 'Partner', 'Rifter', 'Traveller'],
            'Renault' => ['Kangoo', 'Master', 'Trafic'],
            'Toyota' => ['Hilux', 'Proace', 'Proace City', 'Proace Verso'],
            'Volkswagen' => ['Caddy', 'California', 'Caravelle', 'Crafter', 'Grand California', 'Multivan', 'Transporter', 'ID. Buzz'],
            'Other' => [],
        ];

        foreach ($vanMakes as $makeName => $models) {
            $this->createMakeWithModels($makeName, 'van', $models);
        }
    }

    private function seedCaravanMakes(): void
    {
        $caravanMakes = [
            'Adria' => ['Action', 'Alpina', 'Adora', 'Altea', 'Compact', 'Coral', 'Matrix', 'Sonic', 'Twin'],
            'Burstner' => ['Averso', 'Brevio', 'Campeo', 'City Car', 'Copa', 'Eliseo', 'Ixeo', 'Lineo', 'Lyseo', 'Premio', 'Travel Van'],
            'Carado' => ['A', 'CV', 'I', 'T', 'V'],
            'Carthago' => ['C-Compactline', 'C-Tourer', 'Chic', 'Liner', 'Malibu'],
            'Chausson' => ['Flash', 'First Line', 'Titanium', 'Welcome', 'X'],
            'Concorde' => ['Carver', 'Charisma', 'Liner Plus', 'Reisemobile'],
            'Dethleffs' => ['Advantage', 'Beduin', 'Camper', 'Esprit', 'Globebus', 'Globetrotter', 'Just 90', 'Nomad', 'Pulse', 'Trend'],
            'Eura Mobil' => ['Contura', 'Integra', 'Profila', 'Terrestra', 'Van'],
            'Fendt' => ['Bianco', 'Diamant', 'Saphir', 'Tendenza'],
            'Frankia' => ['I', 'M-Line', 'Neo', 'Platin'],
            'Hobby' => ['De Luxe', 'Excellent', 'Maxia', 'Ontour', 'Optima', 'Premium', 'Siesta', 'Vantana'],
            'Hymer' => ['B-Class', 'Carado', 'Compact', 'DuoMobil', 'Exsis', 'Free', 'Grand Canyon', 'ML-T', 'Sidney', 'T-Class', 'Van'],
            'Knaus' => ['BoxDrive', 'BoxLife', 'BoxStar', 'L!VE', 'Sky Ti', 'Sky Wave', 'Sport', 'Sun Ti', 'Van Ti', 'Yaseo'],
            'LMC' => ['Breezer', 'Cruiser', 'Explorer', 'Musica', 'Style'],
            'Malibu' => ['Charming', 'Genius', 'Van'],
            'McLouis' => ['Fusion', 'MC2', 'Sovereign'],
            'Mobilvetta' => ['Admiral', 'K-Yacht', 'Kea'],
            'Morelo' => ['Empire', 'Grand Empire', 'Home', 'Loft', 'Palace'],
            'Niesmann+Bischoff' => ['Arto', 'Flair', 'iSmove', 'Smove'],
            'Pilote' => ['Galaxy', 'Pacific', 'Reference', 'Van V'],
            'Poessl' => ['2Win', 'Campster', 'D-Line', 'Roadcruiser', 'Summit', 'Trenta', 'Vanster'],
            'Rapido' => ['M', 'Serie 6', 'Serie 8', 'Serie 9', 'V'],
            'Rimor' => ['Evo', 'Horus', 'Seal'],
            'Roller Team' => ['Kronos', 'Livingstone', 'Zefiro'],
            'Sunlight' => ['A', 'Cliff', 'I', 'T', 'V'],
            'Weinsberg' => ['CaraBus', 'CaraCompact', 'CaraCore', 'CaraHome', 'CaraSuite', 'CaraTour', 'X-Cursion'],
            'Westfalia' => ['Amundsen', 'Club Joker', 'Columbus', 'James Cook', 'Jules Verne', 'Kepler', 'Sven Hedin'],
            'Winnebago' => ['Fuse', 'Revel', 'Travato', 'View'],
            'Other' => [],
        ];

        foreach ($caravanMakes as $makeName => $models) {
            $this->createMakeWithModels($makeName, 'caravan', $models);
        }
    }

    private function seedTrailerMakes(): void
    {
        $trailerMakes = [
            'Broshuis' => ['Low Loader', 'Multi-Trailer', 'SL'],
            'Fliegl' => ['ASS', 'DTS', 'SDS', 'Tipper'],
            'Goldhofer' => ['STZ', 'THP'],
            'HRD' => ['Box', 'Container', 'Flatbed', 'Reefer'],
            'Humbaur' => ['Enclosed', 'Flatbed', 'Lowbed', 'Tilt'],
            'Kegel' => ['Box', 'Curtainsider', 'Reefer'],
            'Kogel' => ['Cargo', 'Cool', 'Port 45', 'S24'],
            'Krone' => ['Box Liner', 'Cool Liner', 'Dry Liner', 'Mega Liner', 'Profi Liner'],
            'Nooteboom' => ['OSDS', 'Euro', 'MCO', 'Teletrailer'],
            'Schmitz Cargobull' => ['Curtainsider', 'S.BO', 'S.CS', 'S.KI', 'S.KO', 'S.PR', 'S.RI'],
            'Schwarzmuller' => ['Box', 'Flatbed', 'Reefer', 'Tipper'],
            'Thule' => ['Cargo', 'Sport'],
            'Wielton' => ['Box', 'Curtainsider', 'Flatbed', 'Tipper'],
            'Ifor Williams' => ['Flatbed', 'Horse', 'Livestock', 'Tipper'],
            'Brian James' => ['A4 Transporter', 'Car Go', 'Clubman', 'T6'],
            'Other' => [],
        ];

        foreach ($trailerMakes as $makeName => $models) {
            $this->createMakeWithModels($makeName, 'trailer', $models);
        }
    }

    private function seedConstructionMakes(): void
    {
        $constructionMakes = [
            'Caterpillar' => ['305', '312', '315', '320', '323', '325', '330', '336', '340', '345', '352', '390', '950', 'D6', 'D8', 'D9', 'M318', 'TH417'],
            'Komatsu' => ['PC78', 'PC130', 'PC138', 'PC200', 'PC210', 'PC228', 'PC240', 'PC290', 'PC350', 'PC490', 'WA320', 'WA380', 'WA480'],
            'Volvo' => ['EC140', 'EC160', 'EC210', 'EC220', 'EC250', 'EC300', 'EC350', 'EC380', 'EC480', 'EC750', 'EW160', 'L60', 'L90', 'L120'],
            'Liebherr' => ['A 914', 'A 918', 'A 920', 'L 538', 'L 550', 'L 556', 'L 566', 'LTM', 'R 920', 'R 922', 'R 924', 'R 934', 'R 938', 'R 944', 'R 950', 'R 960'],
            'JCB' => ['3CX', '4CX', '8026', '8030', '8035', '8045', '8055', '8085', '8086', 'JS130', 'JS160', 'JS200', 'JS220', 'JS300', 'JS330', 'TM420'],
            'Hitachi' => ['ZX120', 'ZX130', 'ZX135', 'ZX160', 'ZX210', 'ZX225', 'ZX250', 'ZX300', 'ZX350', 'ZX470', 'ZX520', 'ZX670'],
            'Doosan' => ['DL200', 'DL250', 'DL300', 'DX140', 'DX170', 'DX190', 'DX225', 'DX235', 'DX255', 'DX300', 'DX340', 'DX380', 'DX420', 'DX530'],
            'Case' => ['CX130', 'CX160', 'CX180', 'CX210', 'CX220', 'CX245', 'CX300', 'CX350', 'CX370', 'CX490', 'CX750', '521G', '621G', '721G', '821G', '921G'],
            'Hyundai' => ['HW140', 'HW160', 'HX130', 'HX160', 'HX220', 'HX235', 'HX260', 'HX300', 'HX330', 'HX380', 'HX480', 'HX520', 'HL740', 'HL757', 'HL770'],
            'Kubota' => ['KX016', 'KX019', 'KX027', 'KX037', 'KX042', 'KX057', 'KX080', 'U17', 'U25', 'U27', 'U35', 'U48', 'U55'],
            'Bobcat' => ['E10', 'E17', 'E19', 'E20', 'E26', 'E27', 'E32', 'E35', 'E50', 'E55', 'E60', 'E85', 'S450', 'S510', 'S530', 'S570', 'S590', 'S650', 'S770', 'T590', 'T650', 'T770'],
            'Takeuchi' => ['TB016', 'TB025', 'TB230', 'TB235', 'TB240', 'TB250', 'TB260', 'TB280', 'TB290', 'TB2150', 'TL6', 'TL8', 'TL10', 'TL12'],
            'Manitou' => ['MHT', 'MI', 'MLT', 'MRT', 'MT', 'MTA'],
            'Wacker Neuson' => ['1404', '2503', '3503', '6003', '8003', '9503', 'DT10', 'DT25', 'EZ17', 'EZ26', 'EZ36', 'EZ53', 'EZ80'],
            'Yanmar' => ['B27', 'ViO17', 'ViO20', 'ViO25', 'ViO27', 'ViO33', 'ViO35', 'ViO38', 'ViO45', 'ViO50', 'ViO57', 'ViO80', 'SV08', 'SV17'],
            'Other' => [],
        ];

        foreach ($constructionMakes as $makeName => $models) {
            $this->createMakeWithModels($makeName, 'construction', $models);
        }
    }

    private function seedAgriculturalMakes(): void
    {
        $agriculturalMakes = [
            'John Deere' => ['5075E', '5085M', '5100M', '5125R', '6110M', '6120M', '6130M', '6130R', '6140M', '6155M', '6155R', '6175R', '6195M', '6195R', '6215R', '6230R', '6250R', '7230R', '7250R', '7290R', '7310R', '8R 370', '8R 410', '9R 540', '9RX 640'],
            'Fendt' => ['200 Vario', '211 Vario', '312 Vario', '313 Vario', '516 Vario', '718 Vario', '720 Vario', '724 Vario', '828 Vario', '930 Vario', '936 Vario', '939 Vario', '942 Vario', '1042 Vario', '1050 Vario'],
            'Massey Ferguson' => ['4707', '4708', '4709', '5709', '5710', '5711', '5712', '5713', '6712', '6713', '6714', '6716', '7718', '7719', '7720', '7722', '7724', '7726', '8730', '8732', '8735', '8737', '8740'],
            'New Holland' => ['Boomer', 'T4', 'T5', 'T5.100', 'T5.120', 'T6', 'T6.145', 'T6.175', 'T7', 'T7.245', 'T7.275', 'T7.315', 'T8', 'T8.435', 'T9'],
            'Case IH' => ['Farmall', 'JXU', 'Luxxum', 'Maxxum', 'Optum', 'Puma', 'Quadtrac', 'Vestrum'],
            'Claas' => ['Arion', 'Atos', 'Axion', 'Elios', 'Nexos', 'Scorpion', 'Xerion'],
            'Deutz-Fahr' => ['5D', '5G', '6C', '6G', '7', '8', '9', 'Agrokid', 'Agroplus', 'Agrotrac', 'Agrotron'],
            'Kubota' => ['L1', 'L2', 'M4', 'M5', 'M6', 'M7', 'MK5000'],
            'Valtra' => ['A', 'F', 'G', 'N', 'Q', 'S', 'T'],
            'Same' => ['Dorado', 'Explorer', 'Frutteto', 'Solaris', 'Virtus'],
            'Steyr' => ['4000 Multi', '4100 Expert', '6000 Terrus', 'Kompakt', 'Multi', 'Profi'],
            'Lamborghini' => ['Mach', 'Nitro', 'R6', 'Spark', 'Spire', 'Strike'],
            'McCormick' => ['X1', 'X2', 'X5', 'X6', 'X7', 'X8'],
            'AGCO' => ['DT', 'LT', 'RT', 'TT'],
            'Zetor' => ['Forterra', 'Hortus', 'Major', 'Proxima', 'Utilix'],
            'Other' => [],
        ];

        foreach ($agriculturalMakes as $makeName => $models) {
            $this->createMakeWithModels($makeName, 'agricultural', $models);
        }
    }

    private function seedForkliftMakes(): void
    {
        $forkliftMakes = [
            'Toyota' => ['8FBE', '8FD', '8FG', 'BT Levio', 'BT Reflex', 'BT Staxio', 'Tonero'],
            'Linde' => ['E12', 'E14', 'E16', 'E18', 'E20', 'E25', 'E30', 'E35', 'E40', 'E50', 'E80', 'H20', 'H25', 'H30', 'H35', 'H40', 'H50', 'H80', 'K', 'L', 'R', 'T'],
            'Jungheinrich' => ['DFG', 'EFG', 'EFX', 'EJC', 'EKS', 'ERC', 'ERD', 'ERE', 'ESE', 'ETV', 'TFG'],
            'Still' => ['FM-X', 'RX 20', 'RX 50', 'RX 60', 'RX 70'],
            'Crown' => ['ESR', 'FC', 'RC', 'SC', 'TSP', 'WP', 'WS'],
            'Hyster' => ['E', 'H', 'J', 'R', 'S', 'W'],
            'Yale' => ['ERC', 'ERP', 'GDP', 'GLP', 'MO', 'MR', 'MS'],
            'Caterpillar' => ['2C3000', '2C5000', '2C6000', '2C6500', 'DP', 'EP', 'GP', 'NR', 'NRR', 'NSR'],
            'Mitsubishi' => ['EDR', 'FB', 'FD', 'FG', 'RBNT', 'SBR'],
            'Komatsu' => ['AX', 'BX', 'CX', 'DX', 'EX', 'FD', 'FG'],
            'Clark' => ['C', 'CGC', 'CGH', 'CMP', 'CTM', 'GEX', 'GTS', 'GTX', 'S', 'SRX'],
            'Manitou' => ['ME', 'MI', 'MSI', 'MT', 'MH', 'TMT'],
            'Doosan' => ['B', 'D', 'G'],
            'Nissan' => ['DX', 'EGH', 'FD', 'GGH', 'PD', '1F'],
            'Other' => [],
        ];

        foreach ($forkliftMakes as $makeName => $models) {
            $this->createMakeWithModels($makeName, 'forklift', $models);
        }
    }

    private function seedBusMakes(): void
    {
        $busMakes = [
            'Mercedes-Benz' => ['Citaro', 'Conecto', 'eCitaro', 'Integro', 'Intouro', 'O 530', 'Setra', 'Sprinter', 'Tourismo', 'Travego'],
            'MAN' => ['Lion\'s City', 'Lion\'s Coach', 'Lion\'s Intercity', 'Lion\'s Regio', 'TGE'],
            'Setra' => ['ComfortClass', 'MultiClass', 'TopClass'],
            'Iveco' => ['Crossway', 'Daily', 'Evadys', 'Magelys', 'Urbanway'],
            'Solaris' => ['Alpino', 'InterUrbino', 'Urbino', 'Vacanza'],
            'VDL' => ['Citea', 'Futura', 'Jonckheere'],
            'Volvo' => ['7900', '8900', '9700', '9900', 'B8R', 'B11R'],
            'Scania' => ['Citywide', 'Interlink', 'Touring', 'K'],
            'Neoplan' => ['Cityliner', 'Jetliner', 'Skyliner', 'Starliner', 'Tourliner'],
            'Temsa' => ['HD', 'LD', 'MD', 'Safari', 'TS'],
            'Irizar' => ['i4', 'i6', 'i6S', 'ie bus', 'ie tram'],
            'Irisbus' => ['Arway', 'Crossway', 'Evadys', 'Magelys', 'Recreo'],
            'Other' => [],
        ];

        foreach ($busMakes as $makeName => $models) {
            $this->createMakeWithModels($makeName, 'bus', $models);
        }
    }
}
