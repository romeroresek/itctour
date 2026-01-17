  <?php include __DIR__ . '/header.php'; ?>
<div class="container my-4">
  <table class="table table-striped table-bordered text-center align-middle">
    <thead id="tbl-head" class="table-dark"></thead>
    <tbody id="tbl-body"></tbody>
  </table>
</div>
  <?php include __DIR__ . '/footer.php'; ?>


<script>
const SHEET_ID = "1E2RfMpesCwnYtudOFCF7ia4u50uoG8_xPXJe8wcV79Q";
const GID      = "1324325126";
const RANGE    = "U3:AE8";

const url = `https://docs.google.com/spreadsheets/d/${SHEET_ID}/gviz/tq?tqx=out:json&gid=${GID}&range=${encodeURIComponent(RANGE)}`;

fetch(url)
  .then(r => r.text())
  .then(txt => {
    const json  = JSON.parse(txt.substring(txt.indexOf('{'), txt.lastIndexOf('}') + 1));
    const table = json.table || {};
    const cols  = table.cols || [];
    const rows  = table.rows || [];
    const thead = document.getElementById("tbl-head");
    const tbody = document.getElementById("tbl-body");

    let header = [];
    let startRowIndex = 0;

    // 1) Si gviz trae labels, los uso como encabezado
    if (cols.some(c => (c.label || "").trim() !== "")) {
      header = cols.map(c => c.label || "");
    } else {
      // 2) Si no, uso la primera fila del rango como encabezado
      header = (rows[0]?.c || []).map(c => c ? (c.f ?? c.v ?? "") : "");
      startRowIndex = 1; // saltar esa fila en el cuerpo
    }

    // Render <thead>
    const trHead = document.createElement("tr");
    header.forEach(h => {
      const th = document.createElement("th");
      th.textContent = h;
      trHead.appendChild(th);
    });
    thead.appendChild(trHead);

    // Render <tbody>
    for (let i = startRowIndex; i < rows.length; i++) {
      const r = rows[i];
      const tr = document.createElement("tr");
      const cells = r.c || [];
      const maxLen = Math.max(header.length, cells.length);
      for (let j = 0; j < maxLen; j++) {
        const td = document.createElement("td");
        const cell = cells[j];
        td.textContent = cell ? (cell.f ?? cell.v ?? "") : "";
        tr.appendChild(td);
      }
      tbody.appendChild(tr);
    }

    if (!rows.length) {
      tbody.innerHTML = `<tr><td class="text-muted">No hay datos en ${RANGE}</td></tr>`;
    }
  })
  .catch(err => {
    console.error(err);
    document.getElementById("tbl-body").innerHTML =
      `<tr><td class="text-danger">No se pudo cargar el rango ${RANGE}.</td></tr>`;
  });
</script>


