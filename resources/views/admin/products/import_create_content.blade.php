@csrf
<div class="mb-3">
    <label for="file" class="form-label">Choose Excel File</label>
    {{-- <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.csv" required> --}}
    <input type="file" name="files[]" id="file" class="form-control" accept=".xlsx,.csv" multiple required>
</div>
<div id="import-summary-content" class="mt-3"></div>