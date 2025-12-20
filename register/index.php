<?php
$base = '../';
$active = 'register';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>B&S Floor Supply — Catalog & Schedule</title>
<meta name="description" content="Request the Waterproof LVP catalog, a quick estimate, or schedule an appointment with B&S Floor Supply.">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?=$base?>style.css" />
<!-- Config central -->
<script>
window.WA_NUMBER="16892968515";             // WhatsApp de B&S (solo dígitos)
window.EMAIL_TO="orders@bsfloorsupply.com"; // Correo de pedidos
window.FORMSPREE_ID="";                     // Opcional: si lo colocas, se usará Formspree
// Horario: Lunes-Sábado 9:00–17:00 (slots cada 60 min)
window.BUSINESS_DAYS=[1,2,3,4,5,6]; // 0=Dom
window.BUSINESS_START=9;  // 9 AM
window.BUSINESS_END=17;   // 5 PM
window.SLOT_MINUTES=60;
</script>
</head>
<body class="register-page">
<?php include $base.'includes/header.php'; ?>

<section class="hero">
  <div class="wrap">
    <h1>Get the Waterproof LVP Catalog & Schedule a Visit</h1>
    <p class="sub">Tell us where to send the catalog or pick a time for a showroom/at-home visit. Fast response.</p>
  </div>
</section>

<main class="wrap">
  <div class="grid">
    <!-- Lead / Catalog -->
    <div class="panel">
      <h2 style="margin:0 0 8px;color:#5A2A2E;font-family:Montserrat,sans-serif">Request Catalog / Estimate</h2>
      <form id="leadForm" action="../lead.php" method="post">
        <label>
          Full name*
          <input name="name" required autocomplete="name" />
        </label>
        <label>
          Email*
          <input name="email" type="email" required autocomplete="email" />
        </label>
        <label>
          Phone (WhatsApp)*
          <input name="phone" type="tel" required autocomplete="tel" />
        </label>
        <label>
          ZIP Code
          <input name="zip" id="zipLead" list="zip_list_lead" placeholder="Select your ZIP Code" inputmode="numeric" pattern="\\d*" />
          <datalist id="zip_list_lead" data-zip-list></datalist>
          <small class="help">Service area ZIPs supported by B&amp;S.</small>
        </label>
        <input type="hidden" name="city" id="leadCity" />
        <label class="row">
          Interest
          <select name="service" id="interest">
            <option value="catalog">Send me the catalog (PDF)</option>
            <option value="estimate">I want a quick estimate</option>
            <option value="both">Catalog + Estimate</option>
          </select>
        </label>
        <label class="row">
          Notes (optional)
          <textarea name="message" rows="3" placeholder="Square footage, timeline, preferred color…"></textarea>
        </label>


          
        <label class="row consent" for="consent">
          <input id="consent" type="checkbox" required />I agree to be contacted by B&amp;S Floor Supply regarding my request. <span class="help">(You can unsubscribe anytime.)</span>
        </label>
       

        <div class="row" style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px">
          <button class="btn btn-primary" type="submit">Submit</button>
          <a class="btn btn-ghost" id="waLeadBtn" href="#" target="_blank" rel="noopener">Ask via WhatsApp</a>
        </div>

        <div class="row note" id="leadNote" style="display:none;margin-top:10px"></div>
        <input type="hidden" name="form_name" value="register-lead" />
        <input type="hidden" name="source" value="register" />
      </form>
      <span class="badge">We won't spam you</span>
    </div>

    <!-- Scheduler -->
    <div class="panel">
      <h2 style="margin:0 0 8px;color:#5A2A2E;font-family:Montserrat,sans-serif">Schedule an Appointment</h2>
      <form id="schedForm" action="../lead.php" method="post">
        <label>
          Appointment type
          <select name="appt_type" id="apptType">
            <option>Showroom visit</option>
            <option>At-home consultation</option>
            <option>Virtual call</option>
          </select>
        </label>
        <label>
          ZIP Code
          <input name="zip" id="zipSched" list="zip_list_sched" placeholder="Select your ZIP Code" inputmode="numeric" pattern="\\d*" />
          <datalist id="zip_list_sched" data-zip-list></datalist>
          <small class="help">Service area ZIPs supported by B&amp;S.</small>
        </label>
        <label>
          Date*
          <input name="date" id="date" type="date" required />
        </label>
        <label>
          Duration
          <select name="duration" id="duration">
            <option value="30">30 min</option>
            <option value="60" selected>60 min</option>
            <option value="90">90 min</option>
          </select>
        </label>

        
        <input type="hidden" name="time" id="time" required />
        <input type="hidden" name="name" id="schedName" />
        <input type="hidden" name="email" id="schedEmail" />
        <input type="hidden" name="phone" id="schedPhone" />
        <input type="hidden" name="city" id="schedCity" />
        <input type="hidden" name="service" value="schedule" />
        <input type="hidden" name="form_name" value="register-schedule" />
        <input type="hidden" name="source" value="register" />
        <input type="hidden" name="message" id="schedMessage" />


        <label class="row">
          Notes (optional)
          <textarea name="notes" rows="3" placeholder="Address for at-home, details, parking notes…"></textarea>
        </label>

        <div class="row" style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px">
          <button class="btn btn-primary" type="submit">Confirm Appointment</button>
          <a class="btn btn-ghost" id="waSchedBtn" href="#" target="_blank" rel="noopener">Confirm via WhatsApp</a>
        </div>

        <div class="row note" id="schedNote" style="display:none;margin-top:10px"></div>
      </form>
      <span class="badge">Mon–Sat, 9:00–17:00</span>
    </div>
  </div>
</main>

<?php include $base.'includes/reviews.php'; ?>
<?php include $base.'includes/footer.php'; ?>

<script>
  

  // Lead form behavior
  const leadForm = document.getElementById('leadForm');
  const waLeadBtn = document.getElementById('waLeadBtn');
  const leadNote = document.getElementById('leadNote');
  const schedForm = document.getElementById('schedForm');

  ['name','email','phone'].forEach(fn=>{
    const lf = leadForm.querySelector(`[name="${fn}"]`);
    const sf = schedForm.querySelector(`[name="${fn}"]`);
    if(lf && sf){
      const sync = ()=>{ sf.value = lf.value; };
      lf.addEventListener('input', sync);
      sync();
    }
  });

  function buildLeadWA(){
    const data = new FormData(leadForm);
    const name = (data.get('name')||'').toString().trim();
    const email = (data.get('email')||'').toString().trim();
    const phone = (data.get('phone')||'').toString().trim();
    const zip = (data.get('zip')||'').toString().trim();
    const city = (data.get('city')||'').toString().trim();
    const interest = (data.get('service')||'catalog').toString();
    const notes = (data.get('message')||'').toString().trim();
    const zipLine = zip ? `ZIP Code: ${zip}${city ? ` (${city})` : ''}` : 'ZIP Code:';
    const msg = `Hi! I'd like ${interest.replace('both','the catalog and a quick estimate')}.
Name: ${name}
Email: ${email}
Phone: ${phone}
${zipLine}
Notes: ${notes}`;
    const url = `https://wa.me/${window.WA_NUMBER}?text=${encodeURIComponent(msg)}`;
    waLeadBtn.setAttribute('href', url);
  }
  leadForm.addEventListener('change', buildLeadWA);
  document.addEventListener('DOMContentLoaded', buildLeadWA);

  leadForm.addEventListener('submit', function(ev){
    ev.preventDefault();
    const data = new FormData(leadForm);
    fetch('../lead.php', {method:'POST', body:data})
      .then(r=>r.json())
      .then(_=>{ leadNote.style.display='block'; leadNote.textContent='Thanks! We received your request.'; leadForm.reset(); buildLeadWA(); })
      .catch(_=>{ leadNote.style.display='block'; leadNote.textContent='Could not submit. Please try again or use WhatsApp.'; });
  });

  // Scheduler behavior
  const dateEl = document.getElementById('date');
  const slotsEl = document.getElementById('slots');
  const timeEl = document.getElementById('time');
  const waSchedBtn = document.getElementById('waSchedBtn');
  const schedNote = document.getElementById('schedNote');
  const tzLabel = document.getElementById('tzLabel');

  if (tzLabel) {
    tzLabel.textContent = Intl.DateTimeFormat().resolvedOptions().timeZone;
  }

  const ZIP_ZONE_FILE = '<?=$base?>store/zip_zones.json';
  console.log(ZIP_ZONE_FILE)
  let zipLoadPromise = null;
  let ZIP_DATA = [];

  function loadZipData(){
    if(zipLoadPromise) return zipLoadPromise;
    zipLoadPromise = fetch(ZIP_ZONE_FILE)
      .then(res => res.ok ? res.json() : [])
      .then(data => {
        ZIP_DATA = Array.isArray(data) ? data : [];
        return ZIP_DATA;
      })
      .catch(() => {
        ZIP_DATA = [];
        return ZIP_DATA;
      });
    return zipLoadPromise;
  }

  function resolveZipEntry(zip){
    const normalized = (zip || '').toString().trim();
    return ZIP_DATA.find(z => z.zip === normalized);
  }

  function setupZipInputs(){
    const zipInputs = [
      {input: document.getElementById('zipLead'), city: document.getElementById('leadCity')},
      {input: document.getElementById('zipSched'), city: document.getElementById('schedCity')},
    ];
    loadZipData().then(() => {
      document.querySelectorAll('[data-zip-list]').forEach(list => {
        list.innerHTML = ZIP_DATA.map(z => `<option value="${z.zip}">${z.city || ''}</option>`).join('');
      });
      zipInputs.forEach(({input, city}) => {
        if (!input) return;
        const updateCity = () => {
          const selected = resolveZipEntry(input.value);
          if (city) city.value = selected?.city || '';
          buildLeadWA();
          buildSchedWA();
        };
        updateCity();
        input.addEventListener('input', updateCity);
        input.addEventListener('change', updateCity);
      });
    });
  }

  function setMinDate(){
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth()+1).padStart(2,'0');
    const dd = String(today.getDate()).padStart(2,'0');
    dateEl.min = `${yyyy}-${mm}-${dd}`;
  }

  function isBusinessDay(d){
    return window.BUSINESS_DAYS.includes(d.getDay());
  }

  function buildSlots(){
    if (!slotsEl) return;
    slotsEl.innerHTML='';
    timeEl.value='';
    const val = dateEl.value;
    if(!val) return;
    const d = new Date(val+'T00:00:00');
    if(!isBusinessDay(d)){
      slotsEl.innerHTML = '<div class="help" style="grid-column:1/-1">We are closed on this day. Please pick Mon–Sat.</div>';
      return;
    }
    const start = new Date(d); start.setHours(window.BUSINESS_START,0,0,0);
    const end = new Date(d); end.setHours(window.BUSINESS_END,0,0,0);
    const step = window.SLOT_MINUTES;
    const now = new Date();
    for(let t=new Date(start); t<end; t.setMinutes(t.getMinutes()+step)){
      const disabled = (d.toDateString()===now.toDateString() && t < now);
      const hh = String(t.getHours()).padStart(2,'0');
      const mi = String(t.getMinutes()).padStart(2,'0');
      const label = `${(t.getHours()%12)||12}:${mi} ${t.getHours()<12?'AM':'PM'}`;
      const btn = document.createElement('button');
      btn.type='button'; btn.className='slot'; btn.textContent=label;
      if(disabled){ btn.disabled = true; }
      btn.addEventListener('click', ()=>{
        document.querySelectorAll('.slot[aria-pressed="true"]').forEach(el=>el.setAttribute('aria-pressed','false'));
        btn.setAttribute('aria-pressed','true');
        timeEl.value = `${hh}:${mi}`;
        buildSchedWA();
      });
      slotsEl.appendChild(btn);
    }
  }

  function buildSchedWA(){
    const data = new FormData(schedForm);
    const appt = (data.get('appt_type')||'Showroom visit').toString();
    const zip  = (data.get('zip')||'').toString().trim();
    const city = (data.get('city')||'').toString().trim();
    const date = (data.get('date')||'').toString();
    const time = (data.get('time')||'').toString();
    const dur  = (data.get('duration')||'60').toString();
    const notes= (data.get('notes')||'').toString().trim();
    const zipLine = zip ? `ZIP Code: ${zip}${city ? ` (${city})` : ''}` : 'ZIP Code:';
    const msg = `Hi! I'd like to schedule: ${appt}\n${zipLine}\nDate: ${date}\nTime: ${time}\nDuration: ${dur} min\nNotes: ${notes}`;
    waSchedBtn.setAttribute('href', `https://wa.me/${window.WA_NUMBER}?text=${encodeURIComponent(msg)}`);
  }

  schedForm.addEventListener('change', buildSchedWA);
  dateEl.addEventListener('change', ()=>{ buildSlots(); buildSchedWA(); });
  document.addEventListener('DOMContentLoaded', ()=>{ setMinDate(); buildSlots(); setupZipInputs(); buildLeadWA(); buildSchedWA(); });

  schedForm.addEventListener('submit', function(ev){
    ev.preventDefault();
    const data = new FormData(schedForm);
    if(!data.get('date') || !data.get('time')){
      schedNote.style.display='block'; schedNote.textContent='Please select date and time.'; return;
    }
    if(!data.get('name') || (!data.get('email') && !data.get('phone'))){
      schedNote.style.display='block'; schedNote.textContent='Please complete your contact info in the first form.'; return;
    }
    const zip = (data.get('zip')||'').toString().trim();
    const city = (data.get('city')||'').toString().trim();
    const zipLine = zip ? `ZIP Code: ${zip}${city ? ` (${city})` : ''}` : 'ZIP Code:';
    const msgFull = `Appointment type: ${data.get('appt_type')||''}\n${zipLine}\nDate: ${data.get('date')||''}\nTime: ${data.get('time')||''}\nDuration: ${data.get('duration')||''} min\nNotes: ${(data.get('notes')||'')}`;
    data.set('message', msgFull);
    // Build ICS (calendar file) for convenience
    const dt = new Date(data.get('date')+'T'+data.get('time')+':00');
    const durMin = parseInt(data.get('duration')||'60',10);
    const dtEnd = new Date(dt.getTime()+durMin*60000);
    function fmt(d){ return d.toISOString().replace(/[-:]/g,'').split('.')[0]+'Z'; }
    const ics = [
      'BEGIN:VCALENDAR','VERSION:2.0','PRODID:-//B&S Floor Supply//Scheduler//EN','BEGIN:VEVENT',
      'UID:'+Math.random().toString(36).slice(2)+'@bsfloorsupply.com',
      'DTSTAMP:'+fmt(new Date()),
      'DTSTART:'+fmt(dt),
      'DTEND:'+fmt(dtEnd),
      'SUMMARY:'+('Appointment — '+(data.get('appt_type')||'')),
      'LOCATION:'+ (zip ? `ZIP ${zip}${city ? ' ' + city : ''}` : ''),
      'DESCRIPTION:'+ (data.get('notes')||''),
      'END:VEVENT','END:VCALENDAR'
    ].join('\r\n');
    const blob = new Blob([ics], {type:'text/calendar'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = 'bs-appointment.ics'; a.click();
    setTimeout(()=>URL.revokeObjectURL(url), 2000);

    fetch('../lead.php', {method:'POST', body:data})
      .then(r=>r.json())
      .then(_=>{ schedNote.style.display='block'; schedNote.textContent='Appointment sent. Check your calendar file (ICS).'; })
      .catch(_=>{ schedNote.style.display='block'; schedNote.textContent='Could not send. Please try again or use WhatsApp.'; });
})();


</script>

<!-- SEO JSON-LD -->
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"ContactPage",
  "name":"B&S Floor Supply — Catalog & Schedule",
  "url":"https://www.bsfloorsupply.com/register",
  "publisher":{"@type":"Organization","name":"B&S Floor Supply"}
}
</script>
</body>
</html>
