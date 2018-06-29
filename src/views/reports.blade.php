@extends('admin::base')

@section('header')
@endsection

@section('view')
        <section id="listview">
            @if ($lp->can('read'))
            <div class="content">
                <div class="header">
                    <span>{{ trans('admin::base.report') }}</span>
                </div>
                {!! NickDeKruijk\Admin\Controllers\ReportController::reports($lp) !!}
            </div>
            @endif
        </section>
        @if ($lp->can('read'))
        <section id="editview">
            <div class="header">
                <button type="button" id="report_close" class="button border"><i class="fa fa-ban"></i><span>{{ trans('admin::base.close') }}</span></button>
                <label class="f-right"><span id="current_report"></span></label>
            </div>
            <div class="content report">
                <ul class="upload-progress"></ul>
                <ul class="media"></ul>
            </div>
        </section>
        @endif
@endsection

@section('scripts')
<script>
    $('#listview LI').click(function() {
        $('#listview LI.active').removeClass('active');
        $('#current_report').text($(this).text());
        $(this).addClass('active');
        $('#editview').addClass('expanded');
        editviewLoad($(this).data('url'));
    })
    $('#report_close').click(function() {
        $('#editview').removeClass('expanded');
    })
</script>
@endsection
