function exportTableToExcel(tableID, filename = '') {
  const dataType = 'application/vnd.ms-excel';
  const table = document.getElementById(tableID);
  let tableHTML = '\uFEFF' + table.outerHTML;

  const fecha = new Date().toISOString().slice(0, 10);
  filename = filename ? `${filename}_${fecha}.xls` : `inventario_${fecha}.xls`;

  const downloadLink = document.createElement("a");
  document.body.appendChild(downloadLink);

  if (navigator.msSaveOrOpenBlob) {
    const blob = new Blob([tableHTML], { type: dataType });
    navigator.msSaveOrOpenBlob(blob, filename);
  } else {
    downloadLink.href = 'data:' + dataType + ',' + encodeURIComponent(tableHTML);
    downloadLink.download = filename;
    downloadLink.click();
  }

  document.body.removeChild(downloadLink);
}
