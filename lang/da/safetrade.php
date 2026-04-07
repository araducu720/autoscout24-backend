<?php
// Danish
return [
    'title' => 'Sikker handel', 'subtitle' => 'Sikker handel med køretøjer',
    'listing' => ['title' => 'Bytteannoncer', 'create' => 'Opret annonce', 'edit' => 'Rediger annonce', 'publish' => 'Publicer annonce', 'deactivate' => 'Deaktiver annonce',
        'status' => ['draft' => 'Kladde', 'active' => 'Aktiv', 'inactive' => 'Inaktiv', 'sold' => 'Solgt', 'expired' => 'Udløbet'],
        'fields' => ['make' => 'Mærke', 'model' => 'Model', 'year' => 'Årgang', 'mileage' => 'Kilometertal', 'fuel_type' => 'Brændstoftype', 'transmission' => 'Gearkasse', 'vin' => 'VIN', 'registration' => 'Registreringsnummer', 'exterior_color' => 'Udvendig farve', 'interior_color' => 'Indvendig farve', 'condition' => 'Stand', 'description' => 'Beskrivelse', 'min_price' => 'Minimum acceptabel pris', 'max_price' => 'Maksimal forventet pris', 'location' => 'Placering'],
        'condition' => ['excellent' => 'Fremragende', 'good' => 'God', 'fair' => 'Acceptabel', 'poor' => 'Dårlig']],
        'fields' => ['amount' => 'Budbeløb', 'message' => 'Besked til sælger', 'expires_at' => 'Gyldig til']],
    'transaction' => ['title' => 'Transaktion', 'reference' => 'Reference',
        'status' => ['pending_payment' => 'Afventer betaling', 'payment_uploaded' => 'Betaling uploadet', 'payment_verified' => 'Betaling bekræftet', 'vehicle_handed_over' => 'Køretøj overdraget', 'completed' => 'Afsluttet', 'cancelled' => 'Annulleret', 'disputed' => 'Bestridt'],
        'actions' => ['upload_payment' => 'Upload betalingsbevis', 'confirm_handover' => 'Bekræft overdragelse', 'cancel' => 'Annuller transaktion', 'open_dispute' => 'Åbn tvist'],
        'payment' => ['due_date' => 'Betalingsfrist', 'bank_transfer' => 'Bankoverførsel', 'leasing' => 'Leasing'],
        'bank_details' => ['title' => 'Bankoplysninger', 'bank_name' => 'Banknavn', 'iban' => 'IBAN', 'bic' => 'BIC/SWIFT', 'account_holder' => 'Kontoindehaver']],
    'pickup' => ['title' => 'Afhentning af køretøj', 'propose_dates' => 'Foreslå afhentningsdatoer', 'confirm_date' => 'Bekræft afhentningsdato', 'reschedule' => 'Omplanlæg afhentning',
        'status' => ['pending' => 'Afventende', 'dates_proposed' => 'Datoer foreslået', 'confirmed' => 'Bekræftet', 'rescheduled' => 'Omplanlagt', 'completed' => 'Afsluttet'],
        'fields' => ['address' => 'Afhentningsadresse', 'phone' => 'Kontakttelefon', 'date' => 'Afhentningsdato', 'notes' => 'Noter']],
    'dispute' => ['title' => 'Tvist', 'open' => 'Åbn tvist', 'reference' => 'Tvistens reference',
        'status' => ['open' => 'Åben', 'under_review' => 'Under gennemgang', 'awaiting_info' => 'Afventer information', 'escalated' => 'Eskaleret', 'resolved' => 'Løst', 'closed' => 'Lukket'],
        'types' => ['payment_not_received' => 'Betaling ikke modtaget', 'vehicle_not_as_described' => 'Køretøj svarer ikke til beskrivelsen', 'delivery_issue' => 'Leveringsproblem', 'documentation_problem' => 'Dokumentationsproblem', 'fraud' => 'Mistanke om svindel', 'other' => 'Andet'],
        'priority' => ['low' => 'Lav', 'medium' => 'Mellem', 'high' => 'Høj', 'urgent' => 'Hastende'],
        'resolution' => ['in_favor_seller' => 'Afgjort til fordel for sælger', 'in_favor_dealer' => 'Afgjort til fordel for forhandler', 'mutual_agreement' => 'Gensidig aftale', 'cancelled' => 'Annulleret']],
    'notifications' => [
    'emails' => ['subject' => [
    'errors' => ['not_authorized' => 'Du har ikke tilladelse til at udføre denne handling', 'invalid_status' => 'Denne handling er ikke tilgængelig i den aktuelle status', 'payment_already_uploaded' => 'Betalingsbevis er allerede blevet uploadet',
];
