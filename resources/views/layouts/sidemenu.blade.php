<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            {!! $appbrand->business_brand_logo_on_top_left??'ERP'  !!}
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item {{ request()->is('home') || request()->is('/') ? 'active' : '' }}">
            <a href="{{ url('/') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-apps"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('ai/chat') || request()->is('ai/*') ? 'active' : '' }}">
            <a href="{{ url('ai/chat') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-robot text-primary"></i>
                <div data-i18n="AI Chatbot">AI Chatbot</div>
                <div class="badge bg-label-primary fs-tiny rounded-pill ms-auto">AI</div>
            </a>
        </li>
        @hasanyrole('super_admin|admin')
            <!-- Apps & Pages -->
            <li
                class="menu-item {{ request()->is('users') || request()->is('users/*') || (request()->is('roles') || request()->is('roles/*')) ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-users"></i>
                    <div data-i18n="Manage Users">Manage Users</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('users') || request()->is('users/*') ? 'active' : '' }}">
                        <a href="{{ url('users') }}" class="menu-link">
                            <div data-i18n="Users">Users</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('roles') || request()->is('roles/*') ? 'active' : '' }}">
                        <a href="{{ url('roles') }}" class="menu-link">
                            <div data-i18n="Roles">Roles</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ url('permissions') }}" class="menu-link">
                            <div data-i18n="Permission">Permission</div>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="menu-item {{ request()->is('customers') || request()->is('customers/*') ? 'active' : '' }}">
                <a href="{{ url('customers') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-user-cog"></i>
                    <div data-i18n="Customers">Customers</div>
                </a>
            </li>
        @endhasrole
        @hasrole('office-manager')
            <li
                class="menu-item {{ request()->is('enquiries') || request()->is('enquiries/*') || request()->is('enquiry/*') ? 'active' : '' }}">
                <a href="{{ url('enquiries') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-message-2-question"></i>
                    <div data-i18n="Enquiries">Enquiries</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('leads') || request()->is('leads/*') || request()->is('lead/*') ? 'active' : '' }}">
                <a href="{{ url('leads') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-circle-dotted-letter-l"></i>
                    <div data-i18n="Leads">Leads</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('jobs') || request()->is('jobs/*') || request()->is('job/*') ? 'active' : '' }}">
                <a href="{{ url('jobs') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-briefcase"></i>
                    <div data-i18n="Jobs">Jobs</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('scheduler') || request()->is('scheduler/*') ? 'active' : '' }}">
                <a href="{{ url('scheduler') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-calendar-bolt"></i>
                    <div data-i18n="Scheduler">Job Scheduler</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('job-overviews') || request()->is('job-overviews/*') ? 'active' : '' }}">
                <a href="{{ url('job-overviews') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-file-info"></i>
                    <div data-i18n="Job Overviews">Job Overviews</div>
                </a>
            </li>

            {{-- Settings --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                    <div data-i18n="Settings">Settings</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('customers') || request()->is('customers/*') ? 'active' : '' }}">
                        <a href="{{ url('customers') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-user-cog"></i>
                            <div data-i18n="Customers">Customers</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('jobtypes') || request()->is('jobtypes/*') ? 'active' : '' }}">
                        <a href="{{ url('jobtypes') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-arrows-join"></i>
                            <div data-i18n="Job Type">Job Type</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('mailtemplates') || request()->is('mailtemplates/*') ? 'active' : '' }}">
                        <a href="{{ url('mailtemplates') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-mail-spark"></i>
                            <div data-i18n="Mail Templates">Mail Templates</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('quotationtemplates') || request()->is('quotationtemplates/*') ? 'active' : '' }}">
                        <a href="{{ url('quotationtemplates') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-blockquote"></i>
                            <div data-i18n="Quotation Templates">Quotation Templates</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('users') || request()->is('users/*') ? 'active' : '' }}">
                        <a href="{{ url('users') }}" class="menu-link">
                            <div data-i18n="Manage Users">Manage Users</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Manage Inventory --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                    <div data-i18n="Manage Inventory">Manage Inventory</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('stockwarehouses') || request()->is('stockwarehouses/*') ? 'active' : '' }}">
                        <a href="{{ url('stockwarehouses') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-building-warehouse"></i>
                            <div data-i18n="Stock Warehouses">Stock Warehouses</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('stockcategories') || request()->is('stockcategories/*') ? 'active' : '' }}">
                        <a href="{{ url('stockcategories') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-box"></i>
                            <div data-i18n="Stock Categories">Stock Categories</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('stockattributes') || request()->is('stockattributes/*') ? 'active' : '' }}">
                        <a href="{{ url('stockattributes') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-attribute"></i>
                            <div data-i18n="Stock Attributes">Stock Attributes</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('stockitems') || request()->is('stockitems/*') ? 'active' : '' }}">
                        <a href="{{ url('stockitems') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-box-archive"></i>
                            <div data-i18n="Stock Items">Stock Items</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('stockitemsinventory') || request()->is('stockitemsinventory/*') ? 'active' : '' }}">
                        <a href="{{ url('stockitemsinventory') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-archive"></i>
                            <div data-i18n="Stock Items Inventory">Manage Inventory</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('stockitemrequisitions') || request()->is('stockitemrequisitions/*') ? 'active' : '' }}">
                        <a href="{{ url('stockitemrequisitions') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-clipboard-list"></i>
                            <div data-i18n="Stock Item Requisitions">Requisitions</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Manage Vehicle --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                    <div data-i18n="Layouts">Manage Vehicle</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('vehiclecategories') || request()->is('vehiclecategories/*') ? 'active' : '' }}">
                        <a href="{{ url('vehiclecategories') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-car"></i>
                            <div data-i18n="Vehicle Categories">Vehicle Categories</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('vehicles') || request()->is('vehicles/*') ? 'active' : '' }}">
                        <a href="{{ url('vehicles') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-car"></i>
                            <div data-i18n="Vehicles">Vehicles</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('drivers') || request()->is('drivers/*') ? 'active' : '' }}">
                        <a href="{{ url('drivers') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-user"></i>
                            <div data-i18n="Drivers">Drivers</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('vehicle-checkings') || request()->is('vehicle-checkings/*') ? 'active' : '' }}">
                        <a href="{{ url('vehicle-checkings') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-checks"></i>
                            <div data-i18n="Vehicle Checkings">Vehicle Checkings</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endhasrole
        @hasrole('enquiry-manager')
            <li
                class="menu-item {{ request()->is('enquiries') || request()->is('enquiries/*') || request()->is('enquiry/*') ? 'active' : '' }}">
                <a href="{{ url('enquiries') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-message-2-question"></i>
                    <div data-i18n="Enquiries">Enquiries</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('leads') || request()->is('leads/*') || request()->is('lead/*') ? 'active' : '' }}">
                <a href="{{ url('leads') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-circle-dotted-letter-l"></i>
                    <div data-i18n="Leads">Leads</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('customers') || request()->is('customers/*') ? 'active' : '' }}">
                <a href="{{ url('customers') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-user-cog"></i>
                    <div data-i18n="Customers">Customers</div>
                </a>
            </li>
        @endhasrole
        @hasrole('lead-manager')
            <li
                class="menu-item {{ request()->is('leads') || request()->is('leads/*') || request()->is('lead/*') ? 'active' : '' }}">
                <a href="{{ url('leads') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-circle-dotted-letter-l"></i>
                    <div data-i18n="Leads">Leads</div>
                </a>
            </li>

            <li
                class="menu-item {{ request()->is('jobs') || request()->is('jobs/*') || request()->is('job/*') ? 'active' : '' }}">
                <a href="{{ url('jobs') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-briefcase"></i>
                    <div data-i18n="Jobs">Jobs</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                    <div data-i18n="Settings">Settings</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('customers') || request()->is('customers/*') ? 'active' : '' }}">
                        <a href="{{ url('customers') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-user-cog"></i>
                            <div data-i18n="Customers">Customers</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('quotationtemplates') || request()->is('quotationtemplates/*') ? 'active' : '' }}">
                        <a href="{{ url('quotationtemplates') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-blockquote"></i>
                            <div data-i18n="Quotation Templates">Quotation Templates</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endhasrole
        @hasrole('accounts-manager')
            <li
                class="menu-item {{ request()->is('jobs') || request()->is('jobs/*') || request()->is('job/*') ? 'active' : '' }}">
                <a href="{{ url('jobs') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-briefcase"></i>
                    <div data-i18n="Jobs">Jobs</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('customers') || request()->is('customers/*') ? 'active' : '' }}">
                <a href="{{ url('customers') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-user-cog"></i>
                    <div data-i18n="Customers">Customers</div>
                </a>
            </li>
        @endhasrole
        @hasanyrole(['job-manager', 'scheduler', 'Scheduler', 'scheduler-manager'])
            <li
                class="menu-item {{ request()->is('jobs') || request()->is('jobs/*') || request()->is('job/*') ? 'active' : '' }}">
                <a href="{{ url('jobs') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-briefcase"></i>
                    <div data-i18n="Jobs">Jobs</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('scheduler') || request()->is('scheduler/*') ? 'active' : '' }}">
                <a href="{{ url('scheduler') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-calendar-bolt"></i>
                    <div data-i18n="Scheduler">Scheduler</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('job-overviews') || request()->is('job-overviews/*') ? 'active' : '' }}">
                <a href="{{ url('job-overviews') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-file-info"></i>
                    <div data-i18n="Job Overviews">Job Overviews</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                    <div data-i18n="Settings">Settings</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('customers') || request()->is('customers/*') ? 'active' : '' }}">
                        <a href="{{ url('customers') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-user-cog"></i>
                            <div data-i18n="Customers">Customers</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('job-attributes') || request()->is('job-attributes/*') ? 'active' : '' }}">
                        <a href="{{ url('job-attributes') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-settings-share"></i>
                            <div data-i18n="Job Attributes">Job Attributes</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endhasanyrole
        @hasrole('lead-job-manager')
            <li
                class="menu-item {{ request()->is('leads') || request()->is('leads/*') || request()->is('lead/*') ? 'active' : '' }}">
                <a href="{{ url('leads') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-circle-dotted-letter-l"></i>
                    <div data-i18n="Leads">Leads</div>
                </a>
            </li>

            <li
                class="menu-item {{ request()->is('jobs') || request()->is('jobs/*') || request()->is('job/*') ? 'active' : '' }}">
                <a href="{{ url('jobs') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-briefcase"></i>
                    <div data-i18n="Jobs">Jobs</div>
                </a>
            </li>

            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                    <div data-i18n="Settings">Settings</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('customers') || request()->is('customers/*') ? 'active' : '' }}">
                        <a href="{{ url('customers') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-user-cog"></i>
                            <div data-i18n="Customers">Customers</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('quotationtemplates') || request()->is('quotationtemplates/*') ? 'active' : '' }}">
                        <a href="{{ url('quotationtemplates') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-blockquote"></i>
                            <div data-i18n="Quotation Templates">Quotation Templates</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endhasrole
        @hasrole('inventory-manager')
            <li
                class="menu-item {{ request()->is('stockitemsinventory') || request()->is('stockitemsinventory/*') ? 'active' : '' }}">
                <a href="{{ url('stockitemsinventory') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-archive"></i>
                    <div data-i18n="Stock Items Inventory">Manage Inventory</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('stockitemrequisitions') || request()->is('stockitemrequisitions/*') ? 'active' : '' }}">
                <a href="{{ url('stockitemrequisitions') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-clipboard-list"></i>
                    <div data-i18n="Stock Item Requisitions">Item Requisitions</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                    <div data-i18n="Settings">Settings</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('stockwarehouses') || request()->is('stockwarehouses/*') ? 'active' : '' }}">
                        <a href="{{ url('stockwarehouses') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-building-warehouse"></i>
                            <div data-i18n="Stock Warehouses">Stock Warehouses</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('stockcategories') || request()->is('stockcategories/*') ? 'active' : '' }}">
                        <a href="{{ url('stockcategories') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-box"></i>
                            <div data-i18n="Stock Categories">Stock Categories</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('stockitems') || request()->is('stockitems/*') ? 'active' : '' }}">
                        <a href="{{ url('stockitems') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-box-archive"></i>
                            <div data-i18n="Stock Items">Stock Items</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('stockattributes') || request()->is('stockattributes/*') ? 'active' : '' }}">
                        <a href="{{ url('stockattributes') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-attribute"></i>
                            <div data-i18n="Stock Attributes">Stock Attributes</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endhasrole
        @hasrole('fleet-manager')
            <li class="menu-item {{ request()->is('vehicles') || request()->is('vehicles/*') ? 'active' : '' }}">
                <a href="{{ url('vehicles') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-car"></i>
                    <div data-i18n="Manage Vehicles">Manage Vehicles</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('drivers') || request()->is('drivers/*') ? 'active' : '' }}">
                <a href="{{ url('drivers') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-user"></i>
                    <div data-i18n="Manage Drivers">Manage Drivers</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('vehicle-checkings') || request()->is('vehicle-checkings/*') ? 'active' : '' }}">
                <a href="{{ url('vehicle-checkings') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-checks"></i>
                    <div data-i18n="Vehicle Checkings">Vehicle Checkings</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                    <div data-i18n="Settings">Settings</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('vehiclecategories') || request()->is('vehiclecategories/*') ? 'active' : '' }}">
                        <a href="{{ url('vehiclecategories') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-car"></i>
                            <div data-i18n="Vehicle Categories">Vehicle Categories</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endhasrole
        @hasrole('health-safety-manager')
            @php
                $currentJobtype = request()->query('jobtype');
                $jobtypes = DB::table('job_types')->get();
            @endphp
            <li
                class="menu-item {{ request()->is('fjob-hs-check') || request()->is('fjob-hs-check/*') ? 'active open' : '' }}">
                <a href="{{ url('fjob-hs-check') }}" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-checkup-list"></i>
                    <div data-i18n="Job H&S Check">RAMS (Jobs)</div>
                </a>
                <ul class="menu-sub">
                    @foreach ($jobtypes as $jobtype)
                        <li class="menu-item {{ $currentJobtype === $jobtype->slug ? 'active' : '' }}">
                            <a href="{{ url('fjob-hs-check?jobtype=' . $jobtype->slug) }}" class="menu-link">
                                <div data-i18n="{{ $jobtype->name }}">{{ $jobtype->name }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li class="menu-item {{ request()->is('hs-tools') || request()->is('hs-tools/*') ? 'active' : '' }}">
                <a href="{{ url('hs-tools') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-tool"></i>
                    <div data-i18n="H & S Tools">H & S Tools</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('certificates') || request()->is('certificates/*') ? 'active open' : '' }}">
                <a href="#" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-brand-databricks"></i>
                    <div data-i18n="staff Training">staff Trainings</div>
                </a>
                <ul class="menu-sub">

                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div data-i18n="Training Matrix">Training Matrix</div>
                        </a>
                    </li>
                    <li
                        class="menu-item {{ request()->is('certificates') || request()->is('certificates/*') ? 'active' : '' }}">
                        <a href="{{ url('certificates') }}" class="menu-link">
                            <div data-i18n="Certificates">Certificates</div>
                        </a>
                    </li>

                </ul>
            </li>

            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-layout-sidebar"></i>
                    <div data-i18n="Settings">Settings</div>
                </a>
                <ul class="menu-sub">
                    <li
                        class="menu-item {{ request()->is('hs-checklists') || request()->is('hs-checklists/*') ? 'active' : '' }}">
                        <a href="{{ url('hs-checklists') }}" class="menu-link">
                            <i class="menu-icon icon-base ti tabler-checkup-list"></i>
                            <div data-i18n="Health & Safety Checklists">H&S Checklists</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endhasrole
        @hasrole('staff')
            <li class="menu-item {{ request()->is('tasks') || request()->is('tasks/*') ? 'active' : '' }}">
                <a href="{{ url('tasks') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-tool"></i>
                    <div data-i18n="tasks">Tasks</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('fjob-hs-check') || request()->is('fjob-hs-check/*') ? 'active open' : '' }}">
                <a href="{{ url('fjob-hs-check') }}" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-checkup-list"></i>
                    <div data-i18n="Job H&S Check">RAMS (Jobs)</div>
                </a>
                <ul class="menu-sub">
                    @php
                        $currentJobtype = request()->query('jobtype');
                        $jobtypes = DB::table('job_types')->get();
                    @endphp
                    @foreach ($jobtypes as $jobtype)
                        <li class="menu-item {{ $currentJobtype === $jobtype->slug ? 'active' : '' }}">
                            <a href="{{ url('fjob-hs-check?jobtype=' . $jobtype->slug) }}" class="menu-link">
                                <div data-i18n="{{ $jobtype->name }}">{{ $jobtype->name }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li class="menu-item {{ request()->is('hs-tools') || request()->is('hs-tools/*') ? 'active' : '' }}">
                <a href="{{ url('hs-tools') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-tool"></i>
                    <div data-i18n="H & S Tools">H & S Tools</div>
                </a>
            </li>
            <li
                class="menu-item {{ request()->is('staff_trainings') || request()->is('staff_trainings/*') ? 'active open' : '' }}">
                <a href="{{ url('staff_trainings') }}" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-brand-databricks"></i>
                    <div data-i18n="staff Training">staff Trainings</div>
                </a>
                <ul class="menu-sub">

                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div data-i18n="Training Matrix">Training Matrix</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <div data-i18n="Certificates">Certificates</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endhasrole
        @hasanyrole('super_admin|admin')
            <!-- Apps & Pages -->
            <li
                class="menu-item {{ request()->is('businessinfo') || request()->is('businessinfo/*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-settings"></i>
                    <div data-i18n="Manage Users">Configuration</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('businessinfo') || request()->is('businessinfo/*') ? 'active' : '' }}">
                        <a href="{{ url('businessinfo') }}" class="menu-link">
                            <div data-i18n="Users">Business Info</div>
                        </a>
                    </li>

                </ul>
            </li>
        @endhasrole
</aside>
