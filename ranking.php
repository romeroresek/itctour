  <?php include __DIR__ . '/header.php'; ?>
  <!-- Tabla Ranking Damas Segunda -->
  <div class="container my-4">
    <table class="table table-striped table-bordered text-center align-middle">
      <tbody id="tbl-body"></tbody>
    </table>
  </div>
  <?php include __DIR__ . '/footer.php'; ?>
  <script>
  const SHEET_ID = "1E2RfMpesCwnYtudOFCF7ia4u50uoG8_xPXJe8wcV79Q";
  const GID      = "1324325126";
  const RANGE    = "U2:AE8";

  const url = `https://docs.google.com/spreadsheets/d/${SHEET_ID}/gviz/tq?tqx=out:json&gid=${GID}&range=${encodeURIComponent(RANGE)}`;

  fetch(url)
    .then(r => r.text())
    .then(txt => {
      // Extrae el JSON del wrapper gviz
      const json = JSON.parse(txt.substring(txt.indexOf('{'), txt.lastIndexOf('}') + 1));
      const table = json.table || {};
      const cols  = table.cols || [];
      const rows  = table.rows || [];
      const tbody = document.getElementById("tbl-body");

      // Si gviz trató la PRIMERA FILA como encabezado, reinyectamos esa fila como <tr>
      const hasLabels = cols.some(c => (c.label || "").trim() !== "");
      if (hasLabels) {
        const tr = document.createElement("tr");
        cols.forEach(c => {
          const td = document.createElement("td");
          td.textContent = c.label || "";
          tr.appendChild(td);
        });
        tbody.appendChild(tr);
      }

      // Render del resto de las filas
      rows.forEach(r => {
        const tr = document.createElement("tr");
        const cells = (r.c || []);
        // Asegurar mismo número de celdas que columnas
        for (let i = 0; i < Math.max(cols.length, cells.length); i++) {
          const td = document.createElement("td");
          const cell = cells[i];
          td.textContent = cell ? (cell.f ?? cell.v ?? "") : "";
          tr.appendChild(td);
        }
        tbody.appendChild(tr);
      });

      if (!tbody.children.length) {
        tbody.innerHTML = `<tr><td class="text-muted">No hay datos en ${RANGE}</td></tr>`;
      }
    })
    .catch(err => {
      console.error(err);
      document.getElementById("tbl-body").innerHTML =
        `<tr><td class="text-danger">No se pudo cargar el rango ${RANGE}.</td></tr>`;
    });
  </script>

