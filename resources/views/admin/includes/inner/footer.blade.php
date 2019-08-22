<?php use App\Http\Controllers\BaseController;?>
<footer class="page-footer">
    <div class="footer-copyright">
        <div class="container">
		{{BaseController::GetAdminSettingsValue('copyright_text')}}
        </div>
    </div>
</footer>
