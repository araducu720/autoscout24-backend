<?php

namespace Database\Seeders;

use App\Models\Dealer;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DealerVehicleSeeder extends Seeder
{
    /**
     * Seed dealers from real AutoScout24 scraped data and assign vehicles randomly.
     */
    public function run(): void
    {
        $dealers = [
            ['company' => 'A-Point Audi Centrum Amsterdam', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)20 - 5646464', 'city' => 'AMSTERDAM', 'zip' => '1101 AK', 'country' => 'NL', 'street' => 'Klokkenbergweg 19', 'lat' => 52.3014, 'lng' => 4.93646],
            ['company' => 'Adolf Toferer GmbH & Co KG', 'contact' => 'Gerald Seemayr', 'phone' => '+43 (0)7272 - 255620', 'city' => 'Eferding', 'zip' => '4070', 'country' => 'AT', 'street' => 'Bahnhofstraße 57', 'lat' => 48.29909, 'lng' => 14.01375],
            ['company' => 'AECARS BV', 'contact' => 'Alexander Roelens', 'phone' => '+32 (0)51 - 317565', 'city' => 'Izegem', 'zip' => '8870', 'country' => 'BE', 'street' => 'Rijksweg 4', 'lat' => 50.90634, 'lng' => 3.21393],
            ['company' => 'Aecars bvba', 'contact' => 'Alexander Roelens', 'phone' => '+32 (0)51 - 317565', 'city' => 'Lille', 'zip' => '59000', 'country' => 'FR', 'street' => 'Uniquement sur rendez-vous', 'lat' => 50.62295, 'lng' => 3.03501],
            ['company' => 'Agentur für Oldtimer Michael Fröhlich', 'contact' => 'Michael Froehlich', 'phone' => '+49 (0)176 - 9090290', 'city' => 'Mettmann', 'zip' => '40822', 'country' => 'DE', 'street' => 'Rudolf-Diesel-Straße 2', 'lat' => 51.25272, 'lng' => 6.95182],
            ['company' => 'AHG GmbH & Co. KG', 'contact' => 'Audi Ihr Verkaufsteam der AHG-Gruppe', 'phone' => '+49 (0)3621 - 4504818', 'city' => 'Gotha', 'zip' => '99867', 'country' => 'DE', 'street' => 'Cyrusstr. 22', 'lat' => 50.92308, 'lng' => 10.71446],
            ['company' => 'ALDAUTO AUDI', 'contact' => 'Contáctanos en:', 'phone' => '+34 - 919379620', 'city' => 'COLMENAR VIEJO', 'zip' => '28770', 'country' => 'ES', 'street' => 'CARRETERA MADRID-COLMENAR VIEJO KM 28,400', 'lat' => 40.65096, 'lng' => -3.77617],
            ['company' => 'AR Auto Roth', 'contact' => 'Gökay Aydogan', 'phone' => '+49 (0)7841 - 600012', 'city' => 'Achern', 'zip' => '77855', 'country' => 'DE', 'street' => 'Karl-Bold-Str. 2', 'lat' => 48.63731, 'lng' => 8.05352],
            ['company' => 'Arioli Rent Srl', 'contact' => 'Luigi Arioli', 'phone' => '+39 393 - 2046854', 'city' => 'Roma', 'zip' => '00173', 'country' => 'IT', 'street' => 'Via Francesco Antolisei 6', 'lat' => 41.84726, 'lng' => 12.60638],
            ['company' => 'Audi Hamburg Nord', 'contact' => 'Team Gebrauchtwagen', 'phone' => '+49 (0)40 - 8221634763', 'city' => 'Hamburg', 'zip' => '22419', 'country' => 'DE', 'street' => 'Langenhorner Chaussee 666', 'lat' => 53.67837, 'lng' => 10.00148],
            ['company' => 'Audi Hannover GmbH', 'contact' => 'Ihr Gebrauchtagen-Verkaufsteam', 'phone' => '+49 (0)511 - 7110996612', 'city' => 'Hannover', 'zip' => '30179', 'country' => 'DE', 'street' => 'Vahrenwalder Str. 303', 'lat' => 52.41894, 'lng' => 9.73296],
            ['company' => 'Audi München GmbH', 'contact' => 'Team Verkauf', 'phone' => '+49 (0)89 - 5419488039', 'city' => 'Eching', 'zip' => '85386', 'country' => 'DE', 'street' => 'Heisenbergstrasse 4', 'lat' => 48.30442, 'lng' => 11.6299],
            ['company' => 'Audi Zentrum Aachen Jacobs Automobile GmbH', 'contact' => 'Info Geilenkirchen', 'phone' => '+49 (0)2451 - 98700', 'city' => 'Geilenkirchen', 'zip' => '52511', 'country' => 'DE', 'street' => 'Landstr. 48-50', 'lat' => 50.98285, 'lng' => 6.11976],
            ['company' => 'Audi Zentrum Duisburg', 'contact' => 'Verkaufsteam Gebrauchtwagen', 'phone' => '+49 (0)203 - 668819631', 'city' => 'Duisburg', 'zip' => '47249', 'country' => 'DE', 'street' => 'Düsseldorfer Landstraße 37', 'lat' => 51.38735, 'lng' => 6.75953],
            ['company' => 'Audi Zentrum Erfurt GmbH & Co. KG', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)361 - 39990022', 'city' => 'Erfurt', 'zip' => '99099', 'country' => 'DE', 'street' => 'Hermsdorfer Straße 2a', 'lat' => 50.97282, 'lng' => 11.05865],
            ['company' => 'Audi Zentrum Kassel GmbH & Co.KG', 'contact' => 'Audi NW Verkaufsteam', 'phone' => '+49 (0)561 - 8794409', 'city' => 'Kassel', 'zip' => '34125', 'country' => 'DE', 'street' => 'Dresdener Str. 5', 'lat' => 51.3129, 'lng' => 9.51463],
            ['company' => 'Audi Zentrum Köln', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)221 - 37683378', 'city' => 'Köln', 'zip' => '50968', 'country' => 'DE', 'street' => 'Bonner Str. 328', 'lat' => 50.90454, 'lng' => 6.96404],
            ['company' => 'Audi Zentrum Mülheim', 'contact' => 'Verkaufsteam Gebrauchtwagen', 'phone' => '+49 (0)208 - 880265851', 'city' => 'Mülheim an der Ruhr', 'zip' => '45481', 'country' => 'DE', 'street' => 'Düsseldorfer Str. 261', 'lat' => 51.41472, 'lng' => 6.86798],
            ['company' => 'Audi Zentrum Regensburg', 'contact' => 'Verkaufsteam Audi', 'phone' => '+49 (0)941 - 850979921', 'city' => 'Regensburg', 'zip' => '93059', 'country' => 'DE', 'street' => 'Nordgaustr. 5', 'lat' => 49.02668, 'lng' => 12.1132],
            ['company' => 'Audi Zentrum Ulm Gebrauchtwagen', 'contact' => 'Verkauf Gebrauchtwagen', 'phone' => '+49 (0)731 - 9854998486', 'city' => 'Ulm', 'zip' => '89073', 'country' => 'DE', 'street' => 'Wielandstr. 50', 'lat' => 48.40539, 'lng' => 10.00398],
            ['company' => 'Auto Brass', 'contact' => '', 'phone' => '+33 (0)1 - 85149750', 'city' => 'Paris', 'zip' => '75006', 'country' => 'FR', 'street' => '', 'lat' => 48.85089, 'lng' => 2.33285],
            ['company' => 'Auto Esthofer GesmbH', 'contact' => 'Christian Wieshofer', 'phone' => '+43 (0)7672 - 75112511', 'city' => 'Regau', 'zip' => '4844', 'country' => 'AT', 'street' => 'Am Unterfeld 1', 'lat' => 47.98723, 'lng' => 13.69454],
            ['company' => 'Auto Niedermayer GmbH', 'contact' => 'Andreas Wittmann', 'phone' => '+49 (0)9961 - 941377', 'city' => 'Neukirchen', 'zip' => '94362', 'country' => 'DE', 'street' => 'Bogener Str. 8', 'lat' => 48.97213, 'lng' => 12.75535],
            ['company' => 'Auto Pallavicini Sas', 'contact' => 'Marco Pallavicini', 'phone' => '+39 0362 - 561019', 'city' => 'Lentate sul Seveso', 'zip' => '20823', 'country' => 'IT', 'street' => 'Viale Italia, 49', 'lat' => 45.67216, 'lng' => 9.12938],
            ['company' => 'AUTO SELECT EDITION', 'contact' => '', 'phone' => '+34 - 872452349', 'city' => 'PALAFRUGELL', 'zip' => '17200', 'country' => 'ES', 'street' => 'CALLE BLAS DE OTERO 4', 'lat' => 41.92524, 'lng' => 3.15736],
            ['company' => 'Auto Weber GmbH & Co. KG', 'contact' => 'GW Team', 'phone' => '+49 (0)2525 - 6030051', 'city' => 'Neubeckum', 'zip' => '59269', 'country' => 'DE', 'street' => 'Hauptstraße 190', 'lat' => 51.78665, 'lng' => 8.0303],
            ['company' => 'Autobedrijf Brusselers', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)411 - 672844', 'city' => 'BOXTEL', 'zip' => '5281 RT', 'country' => 'NL', 'street' => 'Kruisbroeksestraat 10', 'lat' => 51.58039, 'lng' => 5.31351],
            ['company' => 'Autobedrijf Hansen', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)478 - 550990', 'city' => 'VENRAY', 'zip' => '5803 AN', 'country' => 'NL', 'street' => 'Keizersveld 2', 'lat' => 51.54242, 'lng' => 5.99253],
            ['company' => 'AUTOflex24 GmbH', 'contact' => 'Maximilian Hahn', 'phone' => '+49 (0)6269 - 428700', 'city' => 'Gundelsheim', 'zip' => '74831', 'country' => 'DE', 'street' => 'Gottlieb-Daimler-Straße 42', 'lat' => 49.27258, 'lng' => 9.15702],
            ['company' => 'Autohandel Sneppe Bv', 'contact' => 'Bjorn Sneppe', 'phone' => '+32 (0)50 - 356028', 'city' => 'Oostkamp', 'zip' => '8020', 'country' => 'BE', 'street' => 'Gaston Roelandtsstraat 48', 'lat' => 51.17739, 'lng' => 3.23416],
            ['company' => 'Autohaus Adam Wolfert GmbH', 'contact' => 'Daniel Wolfert', 'phone' => '+49 (0)6022 - 265550', 'city' => 'Großwallstadt', 'zip' => '63868', 'country' => 'DE', 'street' => 'Grundtalring 28', 'lat' => 49.87387, 'lng' => 9.14839],
            ['company' => 'Autohaus Adelbert Moll GmbH & Co. KG', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)211 - 95985564', 'city' => 'Düsseldorf', 'zip' => '40549', 'country' => 'DE', 'street' => 'Krefelder Straße 117', 'lat' => 51.23598, 'lng' => 6.713],
            ['company' => 'Autohaus an Rhein & Lippe GmbH & Co. KG', 'contact' => 'Frau Wellmann', 'phone' => '+49 (0)40 - 74306669', 'city' => 'Wesel', 'zip' => '46483', 'country' => 'DE', 'street' => 'Oberndorfstr. 1', 'lat' => 51.64966, 'lng' => 6.62588],
            ['company' => 'Autohaus Berger GmbH', 'contact' => 'Thomas Berger', 'phone' => '+43 (0)2822 - 52281200', 'city' => 'Zwettl', 'zip' => '3910', 'country' => 'AT', 'street' => 'Kremser Straße 34', 'lat' => 48.60524, 'lng' => 15.17786],
            ['company' => 'Autohaus Fischer GmbH', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)3641 - 4850', 'city' => 'Jena', 'zip' => '07743', 'country' => 'DE', 'street' => 'Brückenstraße 6', 'lat' => 50.95649, 'lng' => 11.61942],
            ['company' => 'Autohaus Kröninger GmbH & Co. KG', 'contact' => 'Verkaufsteam', 'phone' => '+49 (0)6851 - 9930909', 'city' => 'St. Wendel', 'zip' => '66606', 'country' => 'DE', 'street' => 'Welvertstraße 4-6', 'lat' => 49.4694, 'lng' => 7.14689],
            ['company' => 'Autohaus Kurz GmbH', 'contact' => 'Josef Kurz', 'phone' => '+49 (0)7967 - 505', 'city' => 'Rosenberg', 'zip' => '73494', 'country' => 'DE', 'street' => 'Hallerstr. 48', 'lat' => 49.02102, 'lng' => 10.02526],
            ['company' => 'Autohaus Liewers Handel und Service GmbH', 'contact' => 'Oliver Breinsberg', 'phone' => '+43 (0)1 - 61444', 'city' => 'Wien', 'zip' => '1100', 'country' => 'AT', 'street' => 'Triester Straße 87', 'lat' => 48.16808, 'lng' => 16.34935],
            ['company' => 'Autohaus M.Stiglmayr GmbH', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)8441 - 4703103', 'city' => 'Pfaffenhofen', 'zip' => '85276', 'country' => 'DE', 'street' => 'Krankenhausstraße 1', 'lat' => 48.52166, 'lng' => 11.50325],
            ['company' => 'Autohaus Mondschein GmbH', 'contact' => 'Elena Nettuno', 'phone' => '+49 (0)6145 - 5498522', 'city' => 'Flörsheim am Main', 'zip' => '65439', 'country' => 'DE', 'street' => 'Hauptstr. 87a', 'lat' => 50.00865, 'lng' => 8.42254],
            ['company' => 'Autohaus Moser GmbH', 'contact' => 'Sebastian Moser', 'phone' => '+49 (0)89 - 8901510', 'city' => 'Puchheim', 'zip' => '82178', 'country' => 'DE', 'street' => 'Aubinger Weg 47-51', 'lat' => 48.16988, 'lng' => 11.36302],
            ['company' => 'Autohaus Otto Model GmbH & Co. KG', 'contact' => 'Andreas Orban', 'phone' => '+49 (0)7953 - 989828', 'city' => 'Blaufelden-Wiesenbach', 'zip' => '74572', 'country' => 'DE', 'street' => 'Brettheimer Str. 18', 'lat' => 49.29615, 'lng' => 10.03898],
            ['company' => 'Autohaus Pietsch GmbH', 'contact' => 'Oliver Schuldt', 'phone' => '+49 (0)5422 - 9563023', 'city' => 'Melle', 'zip' => '49324', 'country' => 'DE', 'street' => 'Herrenteich 89', 'lat' => 52.20196, 'lng' => 8.35054],
            ['company' => 'Autohaus Schmidt GmbH & Co. KG', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)2306 - 705280', 'city' => 'Lünen', 'zip' => '44534', 'country' => 'DE', 'street' => 'Cappenberger Str. 25a', 'lat' => 51.62001, 'lng' => 7.5244],
            ['company' => 'Autohaus Senker GmbH', 'contact' => 'Autohaus Senker', 'phone' => '+43 (0)2752 - 5010050', 'city' => 'Melk', 'zip' => '3390', 'country' => 'AT', 'street' => 'Abt-Karl-Strasse 80', 'lat' => 48.22271, 'lng' => 15.34482],
            ['company' => 'Autohaus Tabor GmbH Freiburg', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)7841 - 702580921', 'city' => 'Freiburg im Breisgau', 'zip' => '79111', 'country' => 'DE', 'street' => 'Bötzinger Straße 33', 'lat' => 47.98532, 'lng' => 7.78626],
            ['company' => 'Autohaus Tabor GmbH Hamburg', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)7841 - 702580927', 'city' => 'Hamburg', 'zip' => '21079', 'country' => 'DE', 'street' => 'Lewenwerder 2', 'lat' => 53.45674, 'lng' => 9.99919],
            ['company' => 'Autohaus Tabor GmbH Düsseldorf', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)7841 - 702580925', 'city' => 'Düsseldorf', 'zip' => '40235', 'country' => 'DE', 'street' => 'Rosmarinstr. 33', 'lat' => 51.22707, 'lng' => 6.82366],
            ['company' => 'Autohaus Tabor GmbH Berlin', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)7841 - 702580979', 'city' => 'Berlin', 'zip' => '10587', 'country' => 'DE', 'street' => 'Gutenbergstraße 15', 'lat' => 52.51744, 'lng' => 13.32788],
            ['company' => 'Autohaus Tabor GmbH München', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)7841 - 702580923', 'city' => 'München', 'zip' => '81479', 'country' => 'DE', 'street' => 'Aidenbachstraße 141', 'lat' => 48.08837, 'lng' => 11.52037],
            ['company' => 'Autohaus Waldviertel GmbH', 'contact' => '', 'phone' => '+43 (0)2982 - 3955', 'city' => 'Horn', 'zip' => '3580', 'country' => 'AT', 'street' => 'Im Gewerbepark 2-4', 'lat' => 48.6622, 'lng' => 15.64011],
            ['company' => 'Automobil Zentrum Leverkusen GmbH & Co. KG', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)214 - 206545', 'city' => 'Leverkusen', 'zip' => '51373', 'country' => 'DE', 'street' => 'Willy-Brandt-Ring 10', 'lat' => 51.02274, 'lng' => 6.99494],
            ['company' => 'Automobile Schall GmbH', 'contact' => 'Schall', 'phone' => '+49 (0)176 - 38414473', 'city' => 'Gersthofen', 'zip' => '86368', 'country' => 'DE', 'street' => 'Augsburger Straße 53', 'lat' => 48.41723, 'lng' => 10.87848],
            ['company' => 'Automobilgruppe Harz-Leine GmbH', 'contact' => 'Michael Rösner', 'phone' => '+49 (0)5551 - 70070', 'city' => 'Northeim', 'zip' => '37154', 'country' => 'DE', 'street' => 'Harztor 19', 'lat' => 51.70687, 'lng' => 10.00884],
            ['company' => 'Autopol Ruhr', 'contact' => 'Das Verkaufsteam', 'phone' => '+49 (0)209 - 58906176', 'city' => 'Gelsenkirchen', 'zip' => '45879', 'country' => 'DE', 'street' => 'Hiberniastr. 2', 'lat' => 51.50431, 'lng' => 7.09366],
            ['company' => 'AUTOS DIZ', 'contact' => '', 'phone' => '+34 - 886182345', 'city' => 'Vigo', 'zip' => '36213', 'country' => 'ES', 'street' => 'Estrada Camposancos, 123', 'lat' => 42.19742, 'lng' => -8.76383],
            ['company' => 'Autoshopping Srl', 'contact' => 'Autoshopping BDC', 'phone' => '+39 342 - 1422827', 'city' => 'San Vitaliano', 'zip' => '80030', 'country' => 'IT', 'street' => 'Via Nazionale delle Puglie, 100', 'lat' => 40.93109, 'lng' => 14.48893],
            ['company' => 'Autoti di S.e.i.t. Srl', 'contact' => 'Matteo Tironi', 'phone' => '+39 035 - 548379', 'city' => 'Almenno San Bartolomeo', 'zip' => '24030', 'country' => 'IT', 'street' => 'Via Aldo Moro, 1/A', 'lat' => 45.73367, 'lng' => 9.57867],
            ['company' => 'Autowelt Schuler GmbH', 'contact' => '', 'phone' => '+49 (0)7721 - 9494399', 'city' => 'Villingen-Schwenningen', 'zip' => '78052', 'country' => 'DE', 'street' => 'Margarethe-Scherb-Str. 45', 'lat' => 48.073, 'lng' => 8.4693],
            ['company' => 'AVEMO Hanau GmbH', 'contact' => 'Ihr Audi Zentrum Hanau Team', 'phone' => '+49 (0)6181 - 4908555', 'city' => 'Hanau', 'zip' => '63452', 'country' => 'DE', 'street' => 'Luise-Kiesselbach-Straße 17', 'lat' => 50.14891, 'lng' => 8.92793],
            ['company' => 'Bavaria Motors NV', 'contact' => 'Thibault Sandra', 'phone' => '+32 (0)56 - 354001', 'city' => 'Harelbeke', 'zip' => '8530', 'country' => 'BE', 'street' => 'Kortrijksesteenweg 306', 'lat' => 50.84338, 'lng' => 3.2919],
            ['company' => 'bhg Autohandelsgesellschaft mbH', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)7851 - 892524', 'city' => 'Kehl', 'zip' => '77694', 'country' => 'DE', 'street' => 'Straßburger Straße 13', 'lat' => 48.57601, 'lng' => 7.8192],
            ['company' => 'Biesse Auto Srl', 'contact' => 'Paolo Pansa', 'phone' => '+39 393 - 8223752', 'city' => 'Flero', 'zip' => '25020', 'country' => 'IT', 'street' => 'Via Manzoni, 36', 'lat' => 45.26467, 'lng' => 10.15034],
            ['company' => 'Birngruber GmbH', 'contact' => '', 'phone' => '+43 (0)2272 - 69150', 'city' => 'Tulln', 'zip' => '3430', 'country' => 'AT', 'street' => 'Königstetter Straße 169', 'lat' => 48.31939, 'lng' => 16.08457],
            ['company' => 'Borgers Auto-Import B.V.', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)26 - 3253745', 'city' => 'HETEREN', 'zip' => '6666 MG', 'country' => 'NL', 'street' => 'Komkleiland 12', 'lat' => 51.94059, 'lng' => 5.75928],
            ['company' => 'Bourcier Auto Sport', 'contact' => 'Brice Beaufils', 'phone' => '+33 (0)6 - 44604417', 'city' => 'Saint-Barthélemy-d\'Anjou', 'zip' => '49124', 'country' => 'FR', 'street' => '8 Rue de la Chanterie', 'lat' => 47.47639, 'lng' => -0.50375],
            ['company' => 'Breedveld Auto\'s', 'contact' => 'A.G.M. Breedveld', 'phone' => '+31 (0)493 - 242113', 'city' => 'SOMEREN', 'zip' => '5711 DC', 'country' => 'NL', 'street' => 'Lage Akkerweg 4b', 'lat' => 51.38768, 'lng' => 5.73154],
            ['company' => 'Broekhuis Alkmaar Audi', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)72 - 2012576', 'city' => 'ALKMAAR', 'zip' => '1812 RJ', 'country' => 'NL', 'street' => 'Smaragdweg 14', 'lat' => 52.61001, 'lng' => 4.74855],
            ['company' => 'BS AUTO', 'contact' => 'Bruno Scheurer', 'phone' => '+33 (0)3 - 88330909', 'city' => 'SOUFFELWEYERSHEIM', 'zip' => '67460', 'country' => 'FR', 'street' => '51 RUE DES TUILERIES', 'lat' => 48.62383, 'lng' => 7.72822],
            ['company' => 'Cambio Auto FG', 'contact' => 'Giovanni Fiore', 'phone' => '+39 02 - 82396936', 'city' => 'Corsico', 'zip' => '20094', 'country' => 'IT', 'street' => 'Via Cadamosto 1/D', 'lat' => 45.44836, 'lng' => 9.10673],
            ['company' => 'Car & Car Srl', 'contact' => '', 'phone' => '+39 02 - 90030132', 'city' => 'Zibido San Giacomo', 'zip' => '20080', 'country' => 'IT', 'street' => 'Strada Statale dei Giovi', 'lat' => 45.36107, 'lng' => 9.13209],
            ['company' => 'Car Evolution', 'contact' => 'Paride Eros', 'phone' => '+39 0444 - 887166', 'city' => 'Noventa Vicentina', 'zip' => '36025', 'country' => 'IT', 'street' => 'Via Bergoncino, 85', 'lat' => 45.30274, 'lng' => 11.55646],
            ['company' => 'Carprice', 'contact' => 'Riccardo', 'phone' => '+39 347 - 8350330', 'city' => 'Gallarate', 'zip' => '21013', 'country' => 'IT', 'street' => 'Viale Milano, 177', 'lat' => 45.64316, 'lng' => 8.81912],
            ['company' => 'Centro Auto Campano Srl', 'contact' => 'Ufficio Segreteria', 'phone' => '+39 081 - 8195215', 'city' => 'Qualiano', 'zip' => '80019', 'country' => 'IT', 'street' => 'Via Campana 410', 'lat' => 40.91617, 'lng' => 14.14843],
            ['company' => 'Century Groningen', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)50 - 8537100', 'city' => 'GRONINGEN', 'zip' => '9723 AW', 'country' => 'NL', 'street' => 'Bornholmstraat 23-29', 'lat' => 53.21205, 'lng' => 6.59265],
            ['company' => 'COMERCIAL ANACA', 'contact' => '', 'phone' => '+34 - 960365770', 'city' => 'ALAQUAS', 'zip' => '46970', 'country' => 'ES', 'street' => 'C/ Del Roser, 5 - 7', 'lat' => 39.45525, 'lng' => -0.46243],
            ['company' => 'Delvaux Percy', 'contact' => 'Naji Shaban', 'phone' => '+32 (0)81 - 610346', 'city' => 'Gembloux', 'zip' => '5030', 'country' => 'BE', 'street' => 'Chaussée de Wavre 63', 'lat' => 50.57382, 'lng' => 4.69079],
            ['company' => 'DICARS DENIA', 'contact' => '', 'phone' => '+34 - 865734634', 'city' => 'Jávea', 'zip' => '3730', 'country' => 'ES', 'street' => 'C/ Bruselas, 2', 'lat' => 38.78521, 'lng' => 0.17527],
            ['company' => 'Dickerschutz', 'contact' => 'Erwin Dicker', 'phone' => '+31 (0)43 - 4090002', 'city' => 'MAASTRICHT', 'zip' => '6229 PB', 'country' => 'NL', 'street' => 'Molensingel 7-9', 'lat' => 50.82928, 'lng' => 5.71258],
            ['company' => 'Dielle Auto Srl', 'contact' => 'Referente Commerciale', 'phone' => '+39 350 - 1469132', 'city' => 'Borgaro Torinese', 'zip' => '10071', 'country' => 'IT', 'street' => 'Via Lanzo, 28', 'lat' => 45.13571, 'lng' => 7.65624],
            ['company' => 'Domenico Truck Srl', 'contact' => 'Sig. Dino', 'phone' => '+39 0823 - 1686306', 'city' => 'Marcianise', 'zip' => '81025', 'country' => 'IT', 'street' => 'S.S. Sannitica km 87', 'lat' => 41.01268, 'lng' => 14.32319],
            ['company' => 'DOMINGO ALONSO OCASIÓN', 'contact' => '', 'phone' => '+34 - 828810964', 'city' => 'LAS PALMAS DE GRAN CANARIA', 'zip' => '35019', 'country' => 'ES', 'street' => '', 'lat' => 28.10588, 'lng' => -15.44947],
            ['company' => 'DOMINGO ALONSO OCASIÓN TENERIFE', 'contact' => '', 'phone' => '+34 - 822622103', 'city' => 'LA LAGUNA', 'zip' => '38108', 'country' => 'ES', 'street' => '', 'lat' => 28.45839, 'lng' => -16.30117],
            ['company' => 'Drive Market', 'contact' => '', 'phone' => '+352 20 - 803020', 'city' => 'Diekirch', 'zip' => '9230', 'country' => 'LU', 'street' => '34, route d\'Ettelbruck', 'lat' => 49.8614, 'lng' => 6.14496],
            ['company' => 'DRIVER CARS BCN', 'contact' => '', 'phone' => '+34 - 936163667', 'city' => 'CARDEDEU', 'zip' => '8440', 'country' => 'ES', 'street' => 'Poligono Industrial Sud', 'lat' => 41.63709, 'lng' => 2.35924],
            ['company' => 'DRIVER CARS MADRID', 'contact' => '', 'phone' => '+34 - 919374965', 'city' => 'ALCALÁ DE HENARES', 'zip' => '28805', 'country' => 'ES', 'street' => 'VÍA COMPLUTENSE, 103', 'lat' => 40.5007, 'lng' => -3.3369],
            ['company' => 'DRIVER CARS MÁLAGA', 'contact' => '', 'phone' => '+34 - 851903997', 'city' => 'MARBELLA', 'zip' => '29602', 'country' => 'ES', 'street' => 'CTRA ISTAN KM 1', 'lat' => 36.52258, 'lng' => -4.91494],
            ['company' => 'Emil Frey Hans Carstens GmbH', 'contact' => 'Internet Vertriebsteam', 'phone' => '+49 (0)4841 - 9074016', 'city' => 'Husum', 'zip' => '25813', 'country' => 'DE', 'street' => 'Robert-Koch-Straße 32', 'lat' => 54.49554, 'lng' => 9.08622],
            ['company' => 'ErreEsse Auto Srl', 'contact' => 'Roberto Sciortino', 'phone' => '+39 334 - 7791801', 'city' => 'Novara', 'zip' => '28100', 'country' => 'IT', 'street' => 'Strada Biandrate, 60 BIS', 'lat' => 45.45045, 'lng' => 8.59008],
            ['company' => 'EU Neuwagen Knott GmbH', 'contact' => 'Valentina Minute', 'phone' => '+49 (0)8158 - 9148962', 'city' => 'Wielenbach', 'zip' => '82407', 'country' => 'DE', 'street' => 'Ammering 16', 'lat' => 47.87359, 'lng' => 11.15803],
            ['company' => 'FAHR WERK Zell am See', 'contact' => 'FAHR WERK Zell am See', 'phone' => '+43 (0)664 - 88707169', 'city' => 'Zell am See', 'zip' => '5700', 'country' => 'AT', 'street' => 'Brucker Bundesstraße 108', 'lat' => 47.29834, 'lng' => 12.79409],
            ['company' => 'Farina Motors Srl', 'contact' => 'Sig. Leonardo', 'phone' => '+39 02 - 95354410', 'city' => 'Liscate', 'zip' => '20060', 'country' => 'IT', 'street' => 'via Buozzi, 21', 'lat' => 45.48289, 'lng' => 9.38632],
            ['company' => 'Frav Srl', 'contact' => 'Front Office Marketing', 'phone' => '+39 351 - 5626866', 'city' => 'Altavilla Vicentina', 'zip' => '36077', 'country' => 'IT', 'street' => 'Via Olmo, 51/a e 55', 'lat' => 45.52042, 'lng' => 11.47839],
            ['company' => 'G e G Rent srl', 'contact' => 'Fabrizio Gionata', 'phone' => '+39 335 - 8760578', 'city' => 'Fiumicino', 'zip' => '00054', 'country' => 'IT', 'street' => 'Viale Maria, 20', 'lat' => 41.87942, 'lng' => 12.20081],
            ['company' => 'G.& V. Cars srls', 'contact' => 'Enzo Terenzio', 'phone' => '+39 348 - 6158083', 'city' => 'Cassino', 'zip' => '03043', 'country' => 'IT', 'street' => 'Via Ausonia Vecchia, 2', 'lat' => 41.47769, 'lng' => 13.82296],
            ['company' => 'Gabellini Srl', 'contact' => 'Di Oronzo William', 'phone' => '+39 0721 - 279325', 'city' => 'Pesaro', 'zip' => '61122', 'country' => 'IT', 'street' => 'Strada Romagna, 119', 'lat' => 43.91999, 'lng' => 12.85815],
            ['company' => 'Gallery Aaldering', 'contact' => 'Afdeling verkoop', 'phone' => '+31 (0)575 - 564055', 'city' => 'BRUMMEN', 'zip' => '6971 AP', 'country' => 'NL', 'street' => 'Arnhemsestraat 47', 'lat' => 52.08395, 'lng' => 6.15655],
            ['company' => 'Garage Martin Biver S.àr.l.', 'contact' => '', 'phone' => '+352 - 958148', 'city' => 'Wiltz', 'zip' => '9518', 'country' => 'LU', 'street' => '32, route d\'Erpeldange', 'lat' => 49.97452, 'lng' => 5.93961],
            ['company' => 'Gelder & Sorg Coburg GmbH', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)9561 - 404405912', 'city' => 'Coburg', 'zip' => '96450', 'country' => 'DE', 'street' => 'Neustadter Strasse 26', 'lat' => 50.27158, 'lng' => 10.96984],
            ['company' => 'Gems Group Srl', 'contact' => 'Salvatore Formisano', 'phone' => '+39 0815 - 5749465', 'city' => 'Ercolano', 'zip' => '80056', 'country' => 'IT', 'street' => 'Via Cupa Viola, 1', 'lat' => 40.83149, 'lng' => 14.359],
            ['company' => 'Gerhard Schuster GmbH', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)8531 - 97800', 'city' => 'Ruhstorf', 'zip' => '94099', 'country' => 'DE', 'street' => 'Rotthofer Str. 10', 'lat' => 48.42915, 'lng' => 13.32258],
            ['company' => 'Glinicke Automobile Baunatal GmbH & Co.KG', 'contact' => '', 'phone' => '+49 (0)561 - 4990188', 'city' => 'Baunatal', 'zip' => '34225', 'country' => 'DE', 'street' => 'Porschestraße 2-8', 'lat' => 51.26068, 'lng' => 9.42404],
            ['company' => 'GMG AUTO SAINT-TROPEZ', 'contact' => 'Service Commercial', 'phone' => '+33 (0)4 - 89256363', 'city' => 'Gassin', 'zip' => '83580', 'country' => 'FR', 'street' => 'Quartier Bertaud', 'lat' => 43.26222, 'lng' => 6.6005],
            ['company' => 'Gottfried Schultz Automobilhandels SE', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)2131 - 77696971', 'city' => 'Neuss', 'zip' => '41464', 'country' => 'DE', 'street' => 'Jülicher Landstraße 41-43', 'lat' => 51.18768, 'lng' => 6.68446],
            ['company' => 'Grupo Solera Burgos', 'contact' => '', 'phone' => '+34 - 947851269', 'city' => 'BURGOS', 'zip' => '09001', 'country' => 'ES', 'street' => 'CRTA. MADRID - IRUN, KM.234,400', 'lat' => 42.28992, 'lng' => -3.70406],
            ['company' => 'Grupo Solera Palencia', 'contact' => '', 'phone' => '+34 - 979290143', 'city' => 'Palencia', 'zip' => '34004', 'country' => 'ES', 'street' => 'C. Andalucía, p-8', 'lat' => 42.00771, 'lng' => -4.51709],
            ['company' => 'GT MOTORS', 'contact' => 'Geoffrey Tricard', 'phone' => '+33 (0)6 - 98536653', 'city' => 'ROYAN', 'zip' => '17200', 'country' => 'FR', 'street' => '62 RUE FRANCOIS ARAGO', 'lat' => 45.63327, 'lng' => -0.9944],
            ['company' => 'Günther Lang GmbH', 'contact' => 'Ing. Günther Lang', 'phone' => '+43 (0)7732 - 2276', 'city' => 'Haag am Hausruck', 'zip' => '4680', 'country' => 'AT', 'street' => 'Reischau 6', 'lat' => 48.19866, 'lng' => 13.63594],
            ['company' => 'Hagenbusch Automobile GmbH', 'contact' => 'Dieter Hagenbusch', 'phone' => '+49 (0)8232 - 74962', 'city' => 'Schwabmuenchen', 'zip' => '86830', 'country' => 'DE', 'street' => 'Franz-Kleinhans-Str. 10', 'lat' => 48.17371, 'lng' => 10.76681],
            ['company' => 'Hahn Automobile GmbH + Co. KG Backnang', 'contact' => '', 'phone' => '+49 (0)7191 - 90111', 'city' => 'Backnang', 'zip' => '71522', 'country' => 'DE', 'street' => 'Weissacher Str. 73', 'lat' => 48.9357, 'lng' => 9.44679],
            ['company' => 'Hahn Automobile GmbH + Co. KG Göppingen', 'contact' => 'Neuwagenteam', 'phone' => '+49 (0)7161 - 962520', 'city' => 'Göppingen', 'zip' => '73037', 'country' => 'DE', 'street' => 'Heininger Straße 16', 'lat' => 48.6965, 'lng' => 9.66423],
            ['company' => 'Hahn Automobile GmbH + Co. KG Esslingen', 'contact' => 'Neuwagenteam', 'phone' => '+49 (0)711 - 93073490', 'city' => 'Esslingen', 'zip' => '73730', 'country' => 'DE', 'street' => 'Alleenstr. 43', 'lat' => 48.72459, 'lng' => 9.34909],
            ['company' => 'Hahn Automobile GmbH + Co. KG Ludwigsburg', 'contact' => 'Neuwagenteam', 'phone' => '+49 (0)7141 - 28520', 'city' => 'Ludwigsburg', 'zip' => '71636', 'country' => 'DE', 'street' => 'Schwieberdinger Str. 140', 'lat' => 48.89035, 'lng' => 9.15875],
            ['company' => 'Hbs Motors Srl', 'contact' => 'HBS MOTORS', 'phone' => '+39 346 - 5876283', 'city' => 'San Benedetto del Tronto', 'zip' => '63074', 'country' => 'IT', 'street' => 'Via Val Tiberina, 8/10', 'lat' => 42.90724, 'lng' => 13.89187],
            ['company' => 'Heger en Veldwijk Automotive', 'contact' => 'Germen Veldwijk', 'phone' => '+31 (0)341 - 449200', 'city' => 'HARDERWIJK', 'zip' => '3845 MB', 'country' => 'NL', 'street' => 'Noorderbreedte 1a', 'lat' => 52.32815, 'lng' => 5.61217],
            ['company' => 'Hinderks Auto\'s', 'contact' => 'Marcel Hinderks', 'phone' => '+31 (0)6 - 15404395', 'city' => 'EMMEN', 'zip' => '7821 AB', 'country' => 'NL', 'street' => 'Kapitein Nemostraat 9b', 'lat' => 52.77084, 'lng' => 6.92121],
            ['company' => 'Hollandse Auto Import Mij B.V. - HAI', 'contact' => 'Afdeling verkoop', 'phone' => '+31 (0)71 - 4029224', 'city' => 'RIJNSBURG', 'zip' => '2231 ZV', 'country' => 'NL', 'street' => 'Floralaan 2', 'lat' => 52.18574, 'lng' => 4.44686],
            ['company' => 'Huiskes-Kokkeler Doetinchem', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)314 - 376300', 'city' => 'DOETINCHEM', 'zip' => '7008 AK', 'country' => 'NL', 'street' => 'Grutbroek 1', 'lat' => 51.97367, 'lng' => 6.27141],
            ['company' => 'Huiskes-Kokkeler Hengelo', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)74 - 2044179', 'city' => 'HENGELO OV', 'zip' => '7554 NG', 'country' => 'NL', 'street' => 'Goudstraat 65', 'lat' => 52.24395, 'lng' => 6.75971],
            ['company' => 'Ing. F. Kuss Autohaus GmbH', 'contact' => '', 'phone' => '+43 (0)316 - 6716110', 'city' => 'Graz', 'zip' => '8045', 'country' => 'AT', 'street' => 'Weinzöttlstraße 7-15', 'lat' => 47.10114, 'lng' => 15.41509],
            ['company' => 'Jacobs Automobile Mönchengladbach GmbH', 'contact' => 'Marcell Eiselein', 'phone' => '+49 (0)2161 - 475950', 'city' => 'Mönchengladbach', 'zip' => '41066', 'country' => 'DE', 'street' => 'Krefelder Straße 674', 'lat' => 51.22367, 'lng' => 6.48408],
            ['company' => 'JF AUTO 4X4', 'contact' => 'Service Commercial', 'phone' => '+33 (0)4 - 90703268', 'city' => 'JONQUIERES', 'zip' => '84150', 'country' => 'FR', 'street' => '88 Avenue de la Libération', 'lat' => 44.11569, 'lng' => 4.89323],
            ['company' => 'Karl Orthuber GmbH', 'contact' => 'Team Orthuber', 'phone' => '+43 (0)2635 - 63171', 'city' => 'Neunkirchen', 'zip' => '2620', 'country' => 'AT', 'street' => 'Augasse 22', 'lat' => 47.72131, 'lng' => 16.09195],
            ['company' => 'Klassische Automobile', 'contact' => 'Bodo van Jüchems', 'phone' => '+49 (0)251 - 326969', 'city' => 'Münster', 'zip' => '48157', 'country' => 'DE', 'street' => 'Gildenstr. 12', 'lat' => 51.97822, 'lng' => 7.70602],
            ['company' => 'Lacar Automobile B.V.', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)6 - 19603227', 'city' => 'RIDDERKERK', 'zip' => '2984 AA', 'country' => 'NL', 'street' => 'Havenkade 3', 'lat' => 51.87156, 'lng' => 4.60878],
            ['company' => 'Le Corner Classic', 'contact' => 'elody casol', 'phone' => '+33 (0)6 - 27098582', 'city' => 'Saint Remy de Provence', 'zip' => '13100', 'country' => 'FR', 'street' => 'Sur RDV', 'lat' => 43.7887, 'lng' => 4.82952],
            ['company' => 'Luigi Ronconi & Figli Srl', 'contact' => 'Aurelio Ronconi', 'phone' => '+39 0425 - 472311', 'city' => 'Rovigo', 'zip' => '45100', 'country' => 'IT', 'street' => 'Viale del Lavoro 3', 'lat' => 45.05256, 'lng' => 11.78328],
            ['company' => 'Maas-De Koning Audi Capelle aan den IJssel', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)10 - 2615451', 'city' => 'CAPELLE AAN DEN IJSSEL', 'zip' => '2905 TX', 'country' => 'NL', 'street' => 'Wormerhoek 5-7', 'lat' => 51.94273, 'lng' => 4.60236],
            ['company' => 'Maas-De Koning Audi Uithoorn', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)297 - 513390', 'city' => 'UITHOORN', 'zip' => '1422 AC', 'country' => 'NL', 'street' => 'Amsterdamseweg 9', 'lat' => 52.23889, 'lng' => 4.83634],
            ['company' => 'Maas-De Koning Moordrecht', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)182 - 204909', 'city' => 'MOORDRECHT', 'zip' => '2841 MJ', 'country' => 'NL', 'street' => 'Grote Esch 60', 'lat' => 52.02231, 'lng' => 4.65904],
            ['company' => 'Magni & Carnevale Motors Srl', 'contact' => 'Mathieu Magni', 'phone' => '+39 02 - 2480926', 'city' => 'Sesto San Giovanni', 'zip' => '20099', 'country' => 'IT', 'street' => 'Via Verdi, 30', 'lat' => 45.53248, 'lng' => 9.23935],
            ['company' => 'Mak Auto & Techniek', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)184 - 601236', 'city' => 'GROOT-AMMERS', 'zip' => '2964 LJ', 'country' => 'NL', 'street' => 'Van Leeuwenhoekweg 1', 'lat' => 51.93799, 'lng' => 4.84783],
            ['company' => 'Markus Kraska Automobile', 'contact' => 'Markus Kraska', 'phone' => '+49 (0)8341 - 9972443', 'city' => 'Germaringen', 'zip' => '87656', 'country' => 'DE', 'street' => 'Carl-Benz-Straße 3', 'lat' => 47.92154, 'lng' => 10.65239],
            ['company' => 'MD Fahrzeughandels GmbH', 'contact' => '', 'phone' => '+43 (0)5576 - 21090', 'city' => 'Altach', 'zip' => '6844', 'country' => 'AT', 'street' => 'Wiesstraße 16', 'lat' => 47.35677, 'lng' => 9.645],
            ['company' => 'Menardi Auto Group Srl', 'contact' => 'Gioachino', 'phone' => '+39 366 - 9747469', 'city' => 'Bernezzo', 'zip' => '12100', 'country' => 'IT', 'street' => 'Via Valle Grana, 106', 'lat' => 44.3998, 'lng' => 7.45588],
            ['company' => 'MFH Mehrmarken-Fahrzeughandel', 'contact' => 'Frank Grimm', 'phone' => '+49 (0)541 - 34371070', 'city' => 'Osnabrück', 'zip' => '49084', 'country' => 'DE', 'street' => 'Heiligenweg 102', 'lat' => 52.27849, 'lng' => 8.08477],
            ['company' => 'ML Consult noleggio di Matteo Lari', 'contact' => 'Matteo Lari', 'phone' => '+39 335 - 8383593', 'city' => 'Montecatini Terme', 'zip' => '51016', 'country' => 'IT', 'street' => 'Viale Simoncini, 4', 'lat' => 43.88594, 'lng' => 10.76486],
            ['company' => 'MY EXCLUSIVE CAR', 'contact' => 'Michel DEMAY', 'phone' => '+33 (0)6 - 09854005', 'city' => 'LISSIEU', 'zip' => '69380', 'country' => 'FR', 'street' => '42, route Nationale 6', 'lat' => 45.86751, 'lng' => 4.74202],
            ['company' => 'MYOTO', 'contact' => 'CLEMENT WIESER', 'phone' => '+33 (0)3 - 88141444', 'city' => 'MOMMENHEIM', 'zip' => '67670', 'country' => 'FR', 'street' => '1 RUE DE LONDRES', 'lat' => 48.75323, 'lng' => 7.66559],
            ['company' => 'MZ Autoimport e.U.', 'contact' => 'Martin Zoncsich', 'phone' => '+43 (0)699 - 12871490', 'city' => 'Wiener Neustadt', 'zip' => '2700', 'country' => 'AT', 'street' => 'Anny Wödl Gasse 1', 'lat' => 47.80577, 'lng' => 16.2644],
            ['company' => 'Neubauer GmbH', 'contact' => '', 'phone' => '+43 (0)3577 - 22583', 'city' => 'Aichdorf', 'zip' => '8753', 'country' => 'AT', 'street' => 'Bundesstrasse 10', 'lat' => 47.19457, 'lng' => 14.71234],
            ['company' => 'Neuberger Mobile', 'contact' => 'Daniel Neuberger', 'phone' => '+49 (0)176 - 56895993', 'city' => 'St.Wendel', 'zip' => '66606', 'country' => 'DE', 'street' => 'Am Hottenwald 5', 'lat' => 49.48606, 'lng' => 7.12534],
            ['company' => 'Noleggio 24 Srl', 'contact' => 'Consulente Vendite', 'phone' => '+39 339 - 3380003', 'city' => 'Roma', 'zip' => '00141', 'country' => 'IT', 'street' => 'Via dei Prati Fiscali, 403', 'lat' => 41.94187, 'lng' => 12.5093],
            ['company' => 'Oldtimerverkauf.cc Classics', 'contact' => 'Martin Obmann', 'phone' => '+43 (0)650 - 6853322', 'city' => 'Frauental an der Laßnitz', 'zip' => '8523', 'country' => 'AT', 'street' => 'Schulgasse 22/9', 'lat' => 46.82579, 'lng' => 15.25956],
            ['company' => 'Paris Motors Legend', 'contact' => '', 'phone' => '+33 (0)6 - 43399926', 'city' => 'Paris', 'zip' => '75008', 'country' => 'FR', 'street' => 'Rue Marbeuf', 'lat' => 48.86868, 'lng' => 2.30379],
            ['company' => 'Paul\'s classic cars', 'contact' => 'Paul ANCELIN', 'phone' => '+33 (0)2 - 33455824', 'city' => 'Créances', 'zip' => '50710', 'country' => 'FR', 'street' => '26 rue de l\'europe', 'lat' => 49.20027, 'lng' => -1.57825],
            ['company' => 'Percy Motors Automobiles', 'contact' => 'Sasha Dewael', 'phone' => '+32 (0)10 - 238221', 'city' => 'Wavre', 'zip' => '1300', 'country' => 'BE', 'street' => 'Avenue Zenobe Gramme 45', 'lat' => 50.73046, 'lng' => 4.58168],
            ['company' => 'Peter Grampp GmbH & Co. KG', 'contact' => '', 'phone' => '+49 (0)9352 - 875566', 'city' => 'Lohr am Main', 'zip' => '97816', 'country' => 'DE', 'street' => 'Bgm.-Dr.-Nebel-Str. 19', 'lat' => 49.98151, 'lng' => 9.57494],
            ['company' => 'Petrol Cave bv', 'contact' => 'Bart De Winter', 'phone' => '+32 (0)475 - 303986', 'city' => 'Bornem', 'zip' => '2880', 'country' => 'BE', 'street' => 'Enkel op afspraak', 'lat' => 51.09985, 'lng' => 4.24223],
            ['company' => 'Pon Center Amersfoort', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)85 - 0714800', 'city' => 'AMERSFOORT', 'zip' => '3812 RX', 'country' => 'NL', 'street' => 'Industrieweg 11', 'lat' => 52.16413, 'lng' => 5.37296],
            ['company' => 'Pon Center Utrecht Audi', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)30 - 6969900', 'city' => 'DE MEERN', 'zip' => '3454 PE', 'country' => 'NL', 'street' => 'Landzigt 8', 'lat' => 52.07359, 'lng' => 5.04515],
            ['company' => 'Porsche Alpenstraße', 'contact' => '', 'phone' => '+43 (0)505 - 91151270', 'city' => 'Salzburg', 'zip' => '5020', 'country' => 'AT', 'street' => 'Alpenstraße 175', 'lat' => 47.76149, 'lng' => 13.07256],
            ['company' => 'Porsche Deutschlandsberg', 'contact' => 'Mario Skrabal', 'phone' => '+43 (0)505 - 91147', 'city' => 'Deutschlandsberg', 'zip' => '8530', 'country' => 'AT', 'street' => 'Frauentalerstraße 59', 'lat' => 46.8185, 'lng' => 15.22372],
            ['company' => 'Porsche Graz-Kärntnerstraße', 'contact' => '', 'phone' => '+43 (0)50 - 591143271', 'city' => 'Graz', 'zip' => '8020', 'country' => 'AT', 'street' => 'Kärntnerstraße 20', 'lat' => 47.06212, 'lng' => 15.42109],
            ['company' => 'Porsche Graz-Liebenau', 'contact' => 'Gebrauchtwagen - Team', 'phone' => '+43 (0)505 - 91140', 'city' => 'Graz', 'zip' => '8041', 'country' => 'AT', 'street' => 'Ferdinand-Porsche-Platz 1', 'lat' => 47.04049, 'lng' => 15.4663],
            ['company' => 'Porsche Inter Auto GmbH Wien', 'contact' => 'Josef Pinter', 'phone' => '+43 (0)505 - 91117927', 'city' => 'Wien', 'zip' => '1230', 'country' => 'AT', 'street' => 'Ketzergasse 120', 'lat' => 48.12918, 'lng' => 16.30018],
            ['company' => 'Porsche Inter Auto GmbH Innsbruck', 'contact' => 'Rene Pak', 'phone' => '+43 (0)505 - 91173', 'city' => 'Innsbruck', 'zip' => '6020', 'country' => 'AT', 'street' => 'Hallerstraße 165', 'lat' => 47.27631, 'lng' => 11.43436],
            ['company' => 'Porsche Leibnitz', 'contact' => 'Zlatko Topic', 'phone' => '+43 (0)505 - 91146', 'city' => 'Leibnitz', 'zip' => '8430', 'country' => 'AT', 'street' => 'Südbahnstrasse 27', 'lat' => 46.77609, 'lng' => 15.54963],
            ['company' => 'Porsche Leoben', 'contact' => '', 'phone' => '+43 (0)505 - 91141', 'city' => 'St. Peter-Freienstein', 'zip' => '8792', 'country' => 'AT', 'street' => 'Gewerbepark 9', 'lat' => 47.39469, 'lng' => 15.03807],
            ['company' => 'Porsche Muthgasse', 'contact' => 'Hubert Einzinger', 'phone' => '+43 (0)505 - 91110', 'city' => 'Wien', 'zip' => '1190', 'country' => 'AT', 'street' => 'Muthgasse 16 Stiege 1', 'lat' => 48.24634, 'lng' => 16.36713],
            ['company' => 'Porsche Salzburg', 'contact' => 'Gebrauchtwagenteam', 'phone' => '+43 (0)662 - 80715190', 'city' => 'Salzburg', 'zip' => '5020', 'country' => 'AT', 'street' => 'Vogelweiderstraße 69', 'lat' => 47.81618, 'lng' => 13.05328],
            ['company' => 'Porsche Wels', 'contact' => '', 'phone' => '+43 (0)505 - 91136', 'city' => 'Wels', 'zip' => '4600', 'country' => 'AT', 'street' => 'Uhlandstraße 61', 'lat' => 48.18249, 'lng' => 14.07169],
            ['company' => 'Porsche Wien-Donaustadt', 'contact' => 'Aleksej Krekotnev', 'phone' => '+43 (0)505 - 91116270', 'city' => 'Wien', 'zip' => '1220', 'country' => 'AT', 'street' => 'Hirschstettner Straße 38', 'lat' => 48.24084, 'lng' => 16.45883],
            ['company' => 'Porsche Wiener Neustadt', 'contact' => 'Michael Glatz-Wrba', 'phone' => '+43 (0)505 - 91120270', 'city' => 'Wiener Neustadt', 'zip' => '2700', 'country' => 'AT', 'street' => 'Neunkirchner Straße 90', 'lat' => 47.79853, 'lng' => 16.22478],
            ['company' => 'Porsche Zell am See', 'contact' => 'Dalibor Smiljcic', 'phone' => '+43 (0)505 - 91155270', 'city' => 'Zell am See', 'zip' => '5700', 'country' => 'AT', 'street' => 'Prof. Ferry Porsche Straße 1', 'lat' => 47.29448, 'lng' => 12.79576],
            ['company' => 'Pouw Apeldoorn', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)55 - 5395395', 'city' => 'APELDOORN', 'zip' => '7327 AZ', 'country' => 'NL', 'street' => 'Ambachtsveld 1', 'lat' => 52.19336, 'lng' => 5.98542],
            ['company' => 'Pouw Deventer', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)570 - 854854', 'city' => 'DEVENTER', 'zip' => '7418 BG', 'country' => 'NL', 'street' => 'Zweedsestraat 15', 'lat' => 52.24483, 'lng' => 6.19167],
            ['company' => 'Pouw Harderwijk Audi', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)341 - 438380', 'city' => 'HARDERWIJK', 'zip' => '3845 MC', 'country' => 'NL', 'street' => 'Zuiderbreedte 46', 'lat' => 52.32669, 'lng' => 5.61628],
            ['company' => 'Pouw Rijssen', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)548 - 537000', 'city' => 'RIJSSEN', 'zip' => '7461 JM', 'country' => 'NL', 'street' => 'Kalanderstraat 2', 'lat' => 52.32172, 'lng' => 6.50562],
            ['company' => 'Pouw Zwolle', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)38 - 4561300', 'city' => 'ZWOLLE', 'zip' => '8028 PS', 'country' => 'NL', 'street' => 'Lippestraat 5', 'lat' => 52.53875, 'lng' => 6.16621],
            ['company' => 'R. K. Automobile', 'contact' => 'Rene Kümmel', 'phone' => '+49 (0)212 - 2338195', 'city' => 'Solingen', 'zip' => '42651', 'country' => 'DE', 'street' => 'Klingenstraße 214', 'lat' => 51.16229, 'lng' => 7.11115],
            ['company' => 'Reibersdorfer Autowelt GmbH', 'contact' => 'Stefan Kirnstötter', 'phone' => '+43 (0)664 - 8316387', 'city' => 'Obertrum am See', 'zip' => '5162', 'country' => 'AT', 'street' => 'Salzburger Straße 1', 'lat' => 47.93582, 'lng' => 13.08163],
            ['company' => 'Sa.My. Auto', 'contact' => 'Antonio Salerni', 'phone' => '+39 349 - 2587423', 'city' => 'Rende', 'zip' => '87036', 'country' => 'IT', 'street' => 'Via Pigafetta 15/B', 'lat' => 39.35309, 'lng' => 16.24687],
            ['company' => 'Savio Auto Srl', 'contact' => 'Andrea Savio', 'phone' => '+39 376 - 2269888', 'city' => 'Castenedolo', 'zip' => '25104', 'country' => 'IT', 'street' => 'Via dei Ponticelli 103', 'lat' => 45.48057, 'lng' => 10.32318],
            ['company' => 'Schwaba Gebrauchtwagenzentrum', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)821 - 570471861', 'city' => 'Gersthofen', 'zip' => '86368', 'country' => 'DE', 'street' => 'Porschestraße 2', 'lat' => 48.40866, 'lng' => 10.87153],
            ['company' => 'Schwandl Fahrzeug & Vertriebs GmbH', 'contact' => '', 'phone' => '+43 (0)1 - 26066710', 'city' => 'Wien', 'zip' => '1220', 'country' => 'AT', 'street' => 'Wagramer Straße 14', 'lat' => 48.23353, 'lng' => 16.41994],
            ['company' => 'Scuderia 76 srl', 'contact' => 'Francesco', 'phone' => '+39 02 - 49757777', 'city' => 'Milano', 'zip' => '20146', 'country' => 'IT', 'street' => 'Viale Pisa, 37', 'lat' => 45.46318, 'lng' => 9.13712],
            ['company' => 'Senger Bielefeld GmbH', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)521 - 75981011', 'city' => 'Bielefeld', 'zip' => '33609', 'country' => 'DE', 'street' => 'Eckendorfer Straße 23', 'lat' => 52.03255, 'lng' => 8.54785],
            ['company' => 'SG GALLERY, SL', 'contact' => '', 'phone' => '+34 - 876754157', 'city' => 'ZARAGOZA', 'zip' => '50197', 'country' => 'ES', 'street' => 'AVENIDA DIAGONAL PLAZA 14, N34', 'lat' => 41.63568, 'lng' => -0.98986],
            ['company' => 'Sm Motors S.R.L.', 'contact' => 'SM Motors s.r.l.', 'phone' => '+39 039 - 6362276', 'city' => 'Vimercate', 'zip' => '20871', 'country' => 'IT', 'street' => 'Via Brenta, 48', 'lat' => 45.61469, 'lng' => 9.35981],
            ['company' => 'SOLERA MOTOR MADRID', 'contact' => '', 'phone' => '+34 - 919379709', 'city' => 'MADRID', 'zip' => '28050', 'country' => 'ES', 'street' => 'MADRID', 'lat' => 40.50035, 'lng' => -3.66037],
            ['company' => 'SOLERA MOTOR V.W.', 'contact' => '', 'phone' => '+34 - 856632935', 'city' => 'JEREZ DE LA FRONTERA', 'zip' => '11407', 'country' => 'ES', 'street' => 'CTRA. MADRID-CADIZ, KM. 635', 'lat' => 36.70993, 'lng' => -6.12823],
            ['company' => 'Sorbara Auto Srl Limbiate', 'contact' => 'Andrea - Giancarlo', 'phone' => '+39 02 - 99682342', 'city' => 'Limbiate', 'zip' => '20812', 'country' => 'IT', 'street' => 'Viale Monza 2', 'lat' => 45.60662, 'lng' => 9.12431],
            ['company' => 'Sorbara Auto Srl Varedo', 'contact' => 'Federico - Stefano', 'phone' => '+39 0362 - 576084', 'city' => 'Varedo', 'zip' => '20814', 'country' => 'IT', 'street' => 'Via Umberto I 190', 'lat' => 45.59707, 'lng' => 9.14611],
            ['company' => 'Tabor Automobiles Strasbourg', 'contact' => 'Service Commercial', 'phone' => '+33 (0)3 - 68782021', 'city' => 'Strasbourg', 'zip' => '67000', 'country' => 'FR', 'street' => '8 Rue de la Rochelle', 'lat' => 48.54245, 'lng' => 7.7881],
            ['company' => 'Tabor Automobiles Paris', 'contact' => 'Service Commercial', 'phone' => '+33 (0)1 - 87653696', 'city' => 'Paris', 'zip' => '75014', 'country' => 'FR', 'street' => '202 Avenue du Maine', 'lat' => 48.83039, 'lng' => 2.32557],
            ['company' => 'take-your-car GmbH', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)4181 - 21760', 'city' => 'Buchholz in der Nordheide', 'zip' => '21244', 'country' => 'DE', 'street' => 'Bäckerstraße 24', 'lat' => 53.35473, 'lng' => 9.86715],
            ['company' => 'Tepass Schwelm GmbH + Co. KG', 'contact' => 'Jens Meyer', 'phone' => '+49 (0)2336 - 8797055', 'city' => 'Schwelm', 'zip' => '58332', 'country' => 'DE', 'street' => 'Berliner Strasse 60-68', 'lat' => 51.29452, 'lng' => 7.29711],
            ['company' => 'TM AUTO EXCLUSIVE', 'contact' => 'Thomas Mok', 'phone' => '+33 (0)6 - 07529483', 'city' => 'Monistrol sur Loire', 'zip' => '43120', 'country' => 'FR', 'street' => '1 Rue Germaine Tillion', 'lat' => 45.27145, 'lng' => 4.19122],
            ['company' => 'Träger Mobility GmbH', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)178 - 5733351', 'city' => 'Stadt Seeland', 'zip' => '06467', 'country' => 'DE', 'street' => 'Ascherslebener Straße 18f', 'lat' => 51.78475, 'lng' => 11.32736],
            ['company' => 'Tuacar Novara', 'contact' => 'Franco - Fabio', 'phone' => '+39 0321 - 608599', 'city' => 'Novara', 'zip' => '28100', 'country' => 'IT', 'street' => 'Via Sforzesca, 32', 'lat' => 45.43114, 'lng' => 8.6386],
            ['company' => 'Ulrich Senger GmbH Lingen', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)5971 - 79134137', 'city' => 'Lingen', 'zip' => '49809', 'country' => 'DE', 'street' => 'Frerener Str. 27', 'lat' => 52.51882, 'lng' => 7.34108],
            ['company' => 'Ulrich Senger GmbH Rheine', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)5971 - 79134137', 'city' => 'Rheine', 'zip' => '48429', 'country' => 'DE', 'street' => 'Lingener Damm 1', 'lat' => 52.28856, 'lng' => 7.44049],
            ['company' => 'V.N. Motors srl', 'contact' => 'Nicola Laforgia', 'phone' => '+39 392 - 8057436', 'city' => 'Noci', 'zip' => '70015', 'country' => 'IT', 'street' => 'Via Giuseppe di Vittorio, 6/E', 'lat' => 40.79272, 'lng' => 17.13052],
            ['company' => 'Vallei Auto Groep Veenendaal', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)318 - 509960', 'city' => 'VEENENDAAL', 'zip' => '3902 HR', 'country' => 'NL', 'street' => 'Galileistraat 27-29', 'lat' => 52.03797, 'lng' => 5.57527],
            ['company' => 'Van den Brug Drachten', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)512 - 203714', 'city' => 'DRACHTEN', 'zip' => '9201 CH', 'country' => 'NL', 'street' => 'De Lange West 98', 'lat' => 53.10924, 'lng' => 6.08748],
            ['company' => 'Van den Udenhout Den Bosch', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)73 - 6464444', 'city' => 'S-HERTOGENBOSCH', 'zip' => '5232 BT', 'country' => 'NL', 'street' => 'Balkweg 1', 'lat' => 51.70948, 'lng' => 5.32988],
            ['company' => 'Van den Udenhout Eindhoven', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)40 - 2197700', 'city' => 'EINDHOVEN', 'zip' => '5652 AN', 'country' => 'NL', 'street' => 'Wekkerstraat 43-53', 'lat' => 51.43347, 'lng' => 5.42853],
            ['company' => 'Van Mossel Audi Sint-Niklaas', 'contact' => 'Laurens De Wit', 'phone' => '+32 (0)3 - 7601727', 'city' => 'Sint-Niklaas', 'zip' => '9100', 'country' => 'BE', 'street' => 'Grote Baan 80', 'lat' => 51.18362, 'lng' => 4.19754],
            ['company' => 'Van Mossel Audi Tilburg', 'contact' => 'Afdeling verkoop', 'phone' => '+31 (0)13 - 2074339', 'city' => 'Tilburg', 'zip' => '5048 AB', 'country' => 'NL', 'street' => 'Kraaivenstraat 14', 'lat' => 51.5865, 'lng' => 5.0667],
            ['company' => 'VGRB GmbH', 'contact' => 'Gebrauchtwagen Team', 'phone' => '+49 (0)30 - 9627620', 'city' => 'Berlin', 'zip' => '13088', 'country' => 'DE', 'street' => 'Hansastraße 202', 'lat' => 52.55849, 'lng' => 13.4925],
            ['company' => 'WAGENPARQ', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)85 - 2007331', 'city' => 'Purmerend', 'zip' => '1446 TR', 'country' => 'NL', 'street' => 'Ampèrestraat 45', 'lat' => 52.51683, 'lng' => 4.99742],
            ['company' => 'Wealer B.V. Venlo', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)88 - 5677000', 'city' => 'VENLO', 'zip' => '5928 PR', 'country' => 'NL', 'street' => 'Celsiusweg 5', 'lat' => 51.3921, 'lng' => 6.14003],
            ['company' => 'Wittebrug Audi Den Haag', 'contact' => 'Afdeling Verkoop', 'phone' => '+31 (0)70 - 3429333', 'city' => 'DEN HAAG', 'zip' => '2491 BC', 'country' => 'NL', 'street' => 'Donau 96', 'lat' => 52.05916, 'lng' => 4.38946],
            ['company' => 'WWG autowelt GmbH & Co. KG', 'contact' => 'Ihr Verkaufsteam', 'phone' => '+49 (0)176 - 30885528', 'city' => 'Schwäbisch-Gmünd', 'zip' => '73527', 'country' => 'DE', 'street' => 'Im Benzfeld 40', 'lat' => 48.80568, 'lng' => 9.84712],
            ['company' => 'AutoScout24 Luxembourg', 'contact' => '', 'phone' => '', 'city' => 'Arlon', 'zip' => '6700', 'country' => 'BE', 'street' => '', 'lat' => 49.68232, 'lng' => 5.81273],
        ];

        $this->command->info('Creating ' . count($dealers) . ' dealer accounts from real AutoScout24 data...');

        $createdUserIds = [];
        $usedEmails = [];

        foreach ($dealers as $index => $dealer) {
            // Generate unique email from company name
            $slug = Str::slug($dealer['company']);
            if (strlen($slug) > 40) {
                $slug = substr($slug, 0, 40);
                $slug = rtrim($slug, '-');
            }
            $email = $slug . '@autoscout24.com';

            // Ensure email uniqueness
            if (isset($usedEmails[$email])) {
                $usedEmails[$email]++;
                $email = $slug . '-' . $usedEmails[$email] . '@autoscout24.com';
            } else {
                $usedEmails[$email] = 1;
            }

            // Determine contact name for user
            $contactName = !empty($dealer['contact']) ? $dealer['contact'] : $dealer['company'];

            // Create User
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $contactName,
                    'password' => Hash::make('dealer2025!'),
                    'phone' => $dealer['phone'] ?: '',
                    'email_verified_at' => now(),
                    'country' => $dealer['country'],
                ]
            );

            // Create Dealer profile
            Dealer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $dealer['company'],
                    'slug' => Str::slug($dealer['company']) . '-' . $user->id,
                    'address' => $dealer['street'] ?: $dealer['city'],
                    'city' => $dealer['city'],
                    'postal_code' => $dealer['zip'],
                    'country' => $dealer['country'],
                    'phone' => $dealer['phone'] ?: '',
                    'email' => $email,
                    'latitude' => $dealer['lat'],
                    'longitude' => $dealer['lng'],
                    'type' => 'independent',
                    'is_verified' => true,
                    'is_active' => true,
                    'rating' => round(rand(35, 50) / 10, 1),
                    'total_reviews' => rand(5, 150),
                    'verified_at' => now(),
                ]
            );

            $createdUserIds[] = $user->id;
        }

        $this->command->info('Created ' . count($createdUserIds) . ' dealer accounts.');

        // Now assign ALL vehicles to dealers using round-robin for even distribution
        $vehicles = Vehicle::whereNull('user_id')->get();
        $totalVehicles = $vehicles->count();

        if ($totalVehicles === 0) {
            // If all vehicles already have user_id, reassign them anyway
            $vehicles = Vehicle::all();
            $totalVehicles = $vehicles->count();
        }

        $dealerCount = count($createdUserIds);
        $this->command->info("Assigning {$totalVehicles} vehicles evenly to {$dealerCount} dealers (round-robin)...");

        // Shuffle vehicles for variety, then round-robin assign
        $vehicleArray = $vehicles->shuffle()->values();
        foreach ($vehicleArray as $index => $vehicle) {
            $dealerIndex = $index % $dealerCount;
            $vehicle->update(['user_id' => $createdUserIds[$dealerIndex]]);
        }

        $this->command->info("Successfully assigned {$totalVehicles} vehicles to " . count($createdUserIds) . " dealers.");

        // Summary by dealer
        $dealerCounts = Vehicle::whereIn('user_id', $createdUserIds)
            ->selectRaw('user_id, COUNT(*) as count')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $this->command->info("\nTop 10 dealers by vehicle count:");
        foreach ($dealerCounts as $dc) {
            $dealerUser = User::find($dc->user_id);
            $dealerProfile = Dealer::where('user_id', $dc->user_id)->first();
            $name = $dealerProfile ? $dealerProfile->company_name : $dealerUser->name;
            $this->command->info("  {$name}: {$dc->count} vehicles");
        }
    }
}
