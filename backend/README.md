php artisan serve --host 192.168.2.174
return Storage::disk('local')->put($request->company_id . '/Form.pdf', $pdf->output());

sqlite3 extension for ubunut
  - sudo apt-get install php-sqlite3

function getDatesInRange(startDate, endDate) {
    const date = new Date(startDate.getTime());
  
    const dates = [];
  
    // ✅ Exclude end date
    while (date < endDate) {
            let today = new Date(date);
            let [y,m,d] = [today.getDate(),today.getMonth() + 1,today.getFullYear()]

      dates.push(`${y}-${m}-${d}`);
      date.setDate(date.getDate() + 1);
    }
  
    return dates;
  }
  
  const d1 = new Date('2022-01-18');
  const d2 = new Date('2022-01-24');
  
  console.log(getDatesInRange(d1, d2));
