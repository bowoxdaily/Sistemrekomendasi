<!-- partials/footer.blade.php -->
<footer class="footer">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
            Copyright © {{ date('Y') }}.
            {{ App\Models\Setting::get('school_name', 'SMK1 Terisi') }}. All rights reserved.
        </span>
        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">
            <a href="{{ App\Models\Setting::get('school_website', '#') }}" class="text-muted" target="_blank">
                {{ App\Models\Setting::get('school_website', '') }}
            </a>
            <i class="ti-heart text-danger ml-1"></i>
        </span>
    </div>
</footer>
