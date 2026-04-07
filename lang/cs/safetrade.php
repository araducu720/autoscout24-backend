<?php
// Czech
return [
    'title' => 'Bezpečný obchod', 'subtitle' => 'Bezpečné obchodování s vozidly',
    'listing' => ['title' => 'Inzerát na výměnu', 'create' => 'Vytvořit inzerát', 'edit' => 'Upravit inzerát', 'publish' => 'Publikovat inzerát', 'deactivate' => 'Deaktivovat inzerát',
        'status' => ['draft' => 'Koncept', 'active' => 'Aktivní', 'inactive' => 'Neaktivní', 'sold' => 'Prodáno', 'expired' => 'Vypršelo'],
        'fields' => ['make' => 'Značka', 'model' => 'Model', 'year' => 'Rok', 'mileage' => 'Nájezd', 'fuel_type' => 'Typ paliva', 'transmission' => 'Převodovka', 'vin' => 'VIN', 'registration' => 'Registrační číslo', 'exterior_color' => 'Barva exteriéru', 'interior_color' => 'Barva interiéru', 'condition' => 'Stav', 'description' => 'Popis', 'min_price' => 'Minimální přijatelná cena', 'max_price' => 'Maximální očekávaná cena', 'location' => 'Lokalita'],
        'condition' => ['excellent' => 'Výborný', 'good' => 'Dobrý', 'fair' => 'Uspokojivý', 'poor' => 'Špatný']],
        'fields' => ['amount' => 'Částka nabídky', 'message' => 'Zpráva prodávajícímu', 'expires_at' => 'Platná do']],
    'transaction' => ['title' => 'Transakce', 'reference' => 'Reference',
        'status' => ['pending_payment' => 'Čeká na platbu', 'payment_uploaded' => 'Platba nahrána', 'payment_verified' => 'Platba ověřena', 'vehicle_handed_over' => 'Vozidlo předáno', 'completed' => 'Dokončena', 'cancelled' => 'Zrušena', 'disputed' => 'Sporná'],
        'actions' => ['upload_payment' => 'Nahrát doklad o platbě', 'confirm_handover' => 'Potvrdit předání', 'cancel' => 'Zrušit transakci', 'open_dispute' => 'Otevřít spor'],
        'payment' => ['due_date' => 'Splatnost platby', 'bank_transfer' => 'Bankovní převod', 'leasing' => 'Leasing'],
        'bank_details' => ['title' => 'Bankovní údaje', 'bank_name' => 'Název banky', 'iban' => 'IBAN', 'bic' => 'BIC/SWIFT', 'account_holder' => 'Majitel účtu']],
    'pickup' => ['title' => 'Vyzvednutí vozidla', 'propose_dates' => 'Navrhnout termíny vyzvednutí', 'confirm_date' => 'Potvrdit termín vyzvednutí', 'reschedule' => 'Přeplánovat vyzvednutí',
        'status' => ['pending' => 'Čekající', 'dates_proposed' => 'Termíny navrženy', 'confirmed' => 'Potvrzeno', 'rescheduled' => 'Přeplánováno', 'completed' => 'Dokončeno'],
        'fields' => ['address' => 'Adresa vyzvednutí', 'phone' => 'Kontaktní telefon', 'date' => 'Datum vyzvednutí', 'notes' => 'Poznámky']],
    'dispute' => ['title' => 'Spor', 'open' => 'Otevřít spor', 'reference' => 'Reference sporu',
        'status' => ['open' => 'Otevřený', 'under_review' => 'V přezkoumání', 'awaiting_info' => 'Čeká na informace', 'escalated' => 'Eskalováno', 'resolved' => 'Vyřešeno', 'closed' => 'Uzavřeno'],
        'types' => ['payment_not_received' => 'Platba nebyla přijata', 'vehicle_not_as_described' => 'Vozidlo neodpovídá popisu', 'delivery_issue' => 'Problém s dodáním', 'documentation_problem' => 'Problém s dokumentací', 'fraud' => 'Podezření na podvod', 'other' => 'Ostatní'],
        'priority' => ['low' => 'Nízká', 'medium' => 'Střední', 'high' => 'Vysoká', 'urgent' => 'Urgentní'],
        'resolution' => ['in_favor_seller' => 'Vyřešeno ve prospěch prodávajícího', 'in_favor_dealer' => 'Vyřešeno ve prospěch prodejce', 'mutual_agreement' => 'Vzájemná dohoda', 'cancelled' => 'Zrušeno']],
    'notifications' => [
    'emails' => ['subject' => [
    'errors' => ['not_authorized' => 'Nemáte oprávnění provést tuto akci', 'invalid_status' => 'Tato akce není v aktuálním stavu dostupná', 'payment_already_uploaded' => 'Doklad o platbě již byl nahrán',
];
