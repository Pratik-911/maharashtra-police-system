<?= $this->include('admin/layout/header') ?>
<div class="container mt-4">
    <h1>ड्यूटी यादी</h1>
    <p>ही पान सध्या तयार करण्यात आलेली नाही. कृपया पुढील सुधारणा साठी प्रतीक्षा करा.</p>
    <a href="<?= base_url('admin/duties/create') ?>" class="btn btn-primary">नवी ड्यूटी वाटप करा</a>
</div>
<?= $this->include('admin/layout/footer') ?>