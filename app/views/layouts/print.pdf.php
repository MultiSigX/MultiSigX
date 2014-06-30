<?php
header("Content-type: application/pdf");
echo $this->Pdf->Output(QR_OUTPUT_DIR.'MultiSigX.com-'.$printdata['name']."-MSX-Print".".pdf","F");
?>