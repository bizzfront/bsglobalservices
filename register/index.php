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
      <form id="leadForm">
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
          City
          <select name="city">
            <option value="">Select</option>
            <option>Orlando</option>
            <option>Kissimmee</option>
            <option>St. Cloud</option>
            <option>Other</option>
          </select>
        </label>
        <label class="row">
          Interest
          <select name="interest">
            <option value="catalog">Send me the catalog (PDF)</option>
            <option value="estimate">I want a quick estimate</option>
            <option value="both">Catalog + Estimate</option>
          </select>
        </label>
        <label class="row">
          Notes (optional)
          <textarea name="notes" rows="3" placeholder="Square footage, timeline, preferred color…"></textarea>
        </label>

        <div class="row consent">
          <input id="consent" type="checkbox" required />
          <label for="consent">I agree to be contacted by B&amp;S Floor Supply regarding my request. <span class="help">(You can unsubscribe anytime.)</span></label>
        </div>

        <div class="row" style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px">
          <button class="btn btn-primary" type="submit">Submit</button>
          <a class="btn btn-ghost" id="waLeadBtn" href="#" target="_blank" rel="noopener">Ask via WhatsApp</a>
        </div>

        <div class="row note" id="leadNote" style="display:none;margin-top:10px"></div>
      </form>
      <span class="badge">We won't spam you</span>
    </div>

    <!-- Scheduler -->
    <div class="panel">
      <h2 style="margin:0 0 8px;color:#5A2A2E;font-family:Montserrat,sans-serif">Schedule an Appointment</h2>
      <form id="schedForm">
        <label>
          Appointment type
          <select name="appt_type" id="apptType">
            <option>Showroom visit</option>
            <option>At-home consultation</option>
            <option>Virtual call</option>
          </select>
        </label>
        <label>
          Location
          <select name="location">
            <option>Orlando</option>
            <option>Kissimmee</option>
            <option>St. Cloud</option>
          </select>
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

        <div class="row">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <strong>Available times</strong>
            <small id="tzLabel" style="color:#666"></small>
          </div>
          <div id="slots" class="slots" role="group" aria-label="Available time slots"></div>
          <input type="hidden" name="time" id="time" required />
        </div>

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
(function(){
  // Lead form behavior
  const leadForm = document.getElementById('leadForm');
  const waLeadBtn = document.getElementById('waLeadBtn');
  const leadNote = document.getElementById('leadNote');

  function buildLeadWA(){
    const data = new FormData(leadForm);
    const name = (data.get('name')||'').toString().trim();
    const email = (data.get('email')||'').toString().trim();
    const phone = (data.get('phone')||'').toString().trim();
    const city = (data.get('city')||'').toString().trim();
    const interest = (data.get('interest')||'catalog').toString();
    const notes = (data.get('notes')||'').toString().trim();
    const msg = `Hi! I'd like ${interest.replace('both','the catalog and a quick estimate')}.
Name: ${name}
Email: ${email}
Phone: ${phone}
City: ${city}
Notes: ${notes}`;
    const url = `https://wa.me/${window.WA_NUMBER}?text=${encodeURIComponent(msg)}`;
    waLeadBtn.setAttribute('href', url);
  }
  leadForm.addEventListener('change', buildLeadWA);
  document.addEventListener('DOMContentLoaded', buildLeadWA);

  leadForm.addEventListener('submit', function(ev){
    ev.preventDefault();
    const data = new FormData(leadForm);
    if(window.FORMSPREE_ID){
      fetch(`https://formspree.io/f/${window.FORMSPREE_ID}`, {method:'POST', headers:{'Accept':'application/json'}, body:data})
        .then(_=>{ leadNote.style.display='block'; leadNote.textContent='Thanks! We received your request.'; leadForm.reset(); buildLeadWA(); })
        .catch(_=>{ leadNote.style.display='block'; leadNote.textContent='Could not submit. Please try again or use WhatsApp.'; });
    }else if(window.EMAIL_TO){
      const subject = encodeURIComponent('Catalog / Estimate request — B&S Floor Supply');
      const body = encodeURIComponent(Array.from(data.entries()).map(([k,v])=>`${k}: ${v}`).join('\n'));
      window.location.href = `mailto:${window.EMAIL_TO}?subject=${subject}&body=${body}`;
      leadNote.style.display='block'; leadNote.textContent='We are opening your email app. If it does not open, please use WhatsApp.';
    }else{
      leadNote.style.display='block'; leadNote.textContent='No email target configured. Please set EMAIL_TO in config.';
    }
  });

  // Scheduler behavior
  const schedForm = document.getElementById('schedForm');
  const dateEl = document.getElementById('date');
  const slotsEl = document.getElementById('slots');
  const timeEl = document.getElementById('time');
  const waSchedBtn = document.getElementById('waSchedBtn');
  const schedNote = document.getElementById('schedNote');
  const tzLabel = document.getElementById('tzLabel');

  tzLabel.textContent = Intl.DateTimeFormat().resolvedOptions().timeZone;

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
    const loc  = (data.get('location')||'Orlando').toString();
    const date = (data.get('date')||'').toString();
    const time = (data.get('time')||'').toString();
    const dur  = (data.get('duration')||'60').toString();
    const notes= (data.get('notes')||'').toString().trim();
    const msg = `Hi! I'd like to schedule: ${appt}\nLocation: ${loc}\nDate: ${date}\nTime: ${time}\nDuration: ${dur} min\nNotes: ${notes}`;
    waSchedBtn.setAttribute('href', `https://wa.me/${window.WA_NUMBER}?text=${encodeURIComponent(msg)}`);
  }

  schedForm.addEventListener('change', buildSchedWA);
  dateEl.addEventListener('change', ()=>{ buildSlots(); buildSchedWA(); });
  document.addEventListener('DOMContentLoaded', ()=>{ setMinDate(); buildSlots(); buildSchedWA(); });

  schedForm.addEventListener('submit', function(ev){
    ev.preventDefault();
    const data = new FormData(schedForm);
    if(!data.get('date') || !data.get('time')){
      schedNote.style.display='block'; schedNote.textContent='Please select date and time.'; return;
    }
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
      'LOCATION:'+ (data.get('location')||''),
      'DESCRIPTION:'+ (data.get('notes')||''),
      'END:VEVENT','END:VCALENDAR'
    ].join('\r\n');
    const blob = new Blob([ics], {type:'text/calendar'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = 'bs-appointment.ics'; a.click();
    setTimeout(()=>URL.revokeObjectURL(url), 2000);

    if(window.FORMSPREE_ID){
      fetch(`https://formspree.io/f/${window.FORMSPREE_ID}`, {method:'POST', headers:{'Accept':'application/json'}, body:data})
        .then(_=>{ schedNote.style.display='block'; schedNote.textContent='Appointment sent. Check your calendar file (ICS).'; })
        .catch(_=>{ schedNote.style.display='block'; schedNote.textContent='Could not send. Please try again or use WhatsApp.'; });
    }else if(window.EMAIL_TO){
      const subject = encodeURIComponent('New appointment — B&S Floor Supply');
      const body = encodeURIComponent(Array.from(data.entries()).map(([k,v])=>`${k}: ${v}`).join('\n'));
      window.location.href = `mailto:${window.EMAIL_TO}?subject=${subject}&body=${body}`;
      schedNote.style.display='block'; schedNote.textContent='We are opening your email app. ICS file downloaded.';
    }else{
      schedNote.style.display='block'; schedNote.textContent='No email target configured. Please set EMAIL_TO in config.';
    }
  });
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
