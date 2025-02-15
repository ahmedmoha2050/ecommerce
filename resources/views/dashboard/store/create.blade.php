@extends('dashboard.layouts.app')

@section('title')
    {{ __('dashboard/store/create.title') }}
@endsection

@push('css')
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/dashboard/vendor/libs/select2/select2.css') }}"/>
@endpush



@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold py-3 mb-4">
            <a class="text-muted fw-light"
               href="{{ route('dashboard.stores.index') }}">{{ __('dashboard/store/create.breadcrumb_1') }}</a>
            /
            {{ __('dashboard/store/create.breadcrumb_2') }}
        </h4>

        <div class="row">
            <div class="col-md-12">

                <div class="card mb-4">
                    <h5 class="card-header">{{ __('dashboard/store/create.card_header') }}</h5>

                    <div class="card-body">
                        <form action="{{ route('dashboard.stores.store') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                <img src="{{ asset('assets/dashboard/default/category/categories.png') }}"
                                     alt="user-avatar"
                                     class="d-block rounded" height="100" width="100" id="uploadedAvatar"/>
                                <div class="button-wrapper">
                                    <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                        <span class="d-none d-sm-block">
                                            {{ __('dashboard/profile/edit.upload_new_photo') }}
                                        </span>
                                        <i class="bx bx-upload d-block d-sm-none"></i>
                                        <input type="file" id="upload" class="account-file-input" hidden
                                               onchange="loadFile(event)"
                                               name="logo"
                                               accept="image/png, image/jpeg"/>
                                    </label>
                                    <button type="button"
                                            class="btn btn-label-secondary account-image-reset mb-4">
                                        <i class="bx bx-reset d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">{{ __('dashboard/profile/edit.cancel') }}</span>
                                    </button>

                                    <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>

                                    <x-input-error key="logo"/>
                                </div>
                            </div>

                            <hr class="mt-3 mb-4">


                            <div class="nav-align-top  shadow-none">
                                <ul class="nav nav-tabs shadow-none" role="tablist">

                                    @foreach(LaravelLocalization::getLocalesOrder() as $key =>  $locale)
                                        <li class="nav-item shadow-none">
                                            <button type="button"
                                                    @class([
                                                            'nav-link',
                                                            'active' => $key == app()->getLocale(),
                                                             ])
                                                    role="tab"
                                                    data-bs-toggle="tab" data-bs-target="#{{ $locale['name'] }}"
                                                    aria-controls="{{ $locale['name'] }}"
                                                    aria-selected="{{ $key == app()->getLocale() }}">{{ __('lang_key.'.$key) }}
                                            </button>
                                        </li>
                                    @endforeach


                                </ul>
                                <div class="tab-content shadow-none">
                                    @foreach(LaravelLocalization::getLocalesOrder() as $key =>  $locale)
                                        <div
                                                @class([
                                                               'show fade tab-pane shadow-none',
                                                               'active' => $key == app()->getLocale(),
                                                                ])
                                                id="{{ $locale['name'] }}"
                                                role="tabpanel">

                                            <div class="row">
                                                <div class="col-md-6">

                                                    <label for="name"
                                                           class="form-label">{{ __('dashboard/store/create.name',['lang' => __('lang_key.with_'.$key)]) }}</label>
                                                    <input class="form-control" type="text" id="name" name="{{ $key }}[name]"
                                                           placeholder="{{ __('dashboard/store/create.name_placeholder',['lang' => __('lang_key.with_'.$key)]) }}"
                                                           value="{{ old( $key . '.name') }}"/>
                                                    <x-input-error key="{{ $key .'.name'}}"/>

                                                </div>
                                                <div class="col-md-6">
                                                    <label for="description"
                                                           class="form-label">{{ __('dashboard/store/create.description',['lang' => __('lang_key.with_'.$key)]) }}</label>
                                                    <textarea class="form-control" name="{{ $key }}[description]"
                                                              id="description"
                                                              cols="30"
                                                              rows="5">{{ old( $key .'.description') }}</textarea>

                                                    <x-input-error key="{{ $key .'.description'}}" />
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach

                                </div>

                            </div>


                            <div class="card-footer mt-3">
                                <button class="btn btn-primary w-100"
                                        title="{{ __('dashboard/store/create.submit_btn') }}">{{ __('dashboard/store/create.submit_btn') }}</button>
                            </div>


                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.partials.toast')

@endsection

@push('js')

    <!-- Vendors JS -->
    <script src="{{ asset('assets/dashboard/vendor/libs/select2/select2.js') }}"></script>


    <!-- Page JS -->
    <script src="{{ asset('assets/dashboard/js/forms-selects.js') }}"></script>

    <script>
        var loadFile = function (event) {
            var output = document.getElementById('uploadedAvatar');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function () {
                URL.revokeObjectURL(output.src) // free memory
            }
        };
    </script>
@endpush

