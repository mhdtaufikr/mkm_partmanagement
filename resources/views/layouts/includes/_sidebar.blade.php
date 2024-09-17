<div id="layoutSidenav_nav">
    <nav class="sidenav shadow-right sidenav-light">
        <div class="sidenav-menu">
            <div class="nav accordion" id="accordionSidenav">

                <!-- Sidenav Menu Heading (Core) -->
                <div class="sidenav-menu-heading">Core</div>

                <!-- Sidenav Link (Home) -->
                <a class="nav-link" href="{{ url('/home') }}">
                    <div class="nav-link-icon" style="margin-left: -2px"><i class="fas fa-home"></i></div>
                    Home
                </a>

                <!-- Sidenav Link (Daily Report) -->
                <a class="nav-link" href="{{ url('/history') }}">
                    <div class="nav-link-icon" style="margin-left: -2px"><i class="fas fa-tools"></i></div>
                    Daily Report
                </a>

                <!-- Sidenav Link (Preventive Maintenance) -->
                <a class="nav-link" href="{{ url('/checksheet') }}">
                    <div class="nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                    Preventive Maintenance
                </a>

                <!-- Sidenav Link (Preventive Maintenance) -->
                <a class="nav-link" href="{{ url('/summary') }}">
                    <div class="nav-link-icon"><i class="fas fa-book"></i></div>
                    Summary Report
                </a>

                 <!-- Sidenav Link (Preventive Maintenance) -->
                 <a class="nav-link" href="{{ url('/part/info') }}">
                    <div class="nav-link-icon"><i class="fas fa-info"></i></div>
                    Part Info
                </a>

                <!-- Sidenav Menu Heading (Master) -->
                <div class="sidenav-menu-heading">Master</div>

                <!-- Sidenav Accordion (Master Machine) -->
                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapsemachine" aria-expanded="false" aria-controls="collapsemachine">
                    <div class="nav-link-icon"><i class="fas fa-database"></i></div>
                    Master Machine
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <!-- Nested Navigation for Master Machine -->
                <div class="collapse" id="collapsemachine" data-bs-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav">
                        @if(auth()->user()->plant == 'All' || auth()->user()->plant == 'Engine')
                            <a class="nav-link" href="{{ url('/mst/machine/part/engine') }}">Machine Engine</a>
                        @endif
                        @if(auth()->user()->plant == 'All' || auth()->user()->plant == 'Stamping')
                            <a class="nav-link" href="{{ url('/mst/machine/part/stamping') }}">Machine Stamping</a>
                        @endif
                        @if(auth()->user()->plant == 'All')
                            <a class="nav-link" href="{{ url('/mst/machine/part') }}">Machine All</a>
                        @endif
                    </nav>
                </div>

                <!-- Sidenav Accordion (Master Part) -->
                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapsepart" aria-expanded="false" aria-controls="collapsepart">
                    <div class="nav-link-icon"><i class="fas fa-database"></i></div>
                    Master Part
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <!-- Nested Navigation for Master Part -->
                <div class="collapse" id="collapsepart" data-bs-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav">
                        @if(auth()->user()->plant == 'All' || auth()->user()->plant == 'Engine')
                            <a class="nav-link" href="{{ url('/mst/sap/part/P400') }}">Part Engine</a>
                        @endif
                        @if(auth()->user()->plant == 'All' || auth()->user()->plant == 'Stamping')
                            <a class="nav-link" href="{{ url('/mst/sap/part/P300') }}">Part Stamping</a>
                        @endif
                        @if(auth()->user()->plant == 'All')
                            <a class="nav-link" href="{{ url('/mst/sap/part') }}">Part All</a>
                            <a class="nav-link" href="{{ url('/mst/repair/part') }}">Part Repair</a>
                        @endif
                    </nav>
                </div>


                 <!-- Sidenav Accordion (Master PM) -->
                 <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapsemasterSchedule" aria-expanded="false" aria-controls="collapsemasterSchedule">
                    <div class="nav-link-icon"><i class="fas fa-database"></i></div>
                    Master Schedule
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <!-- Nested Navigation for Master PM -->
                <div class="collapse" id="collapsemasterSchedule" data-bs-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ url('/mst/preventive/schedule/me_engine') }}">ME Engine</a>
                        <a class="nav-link" href="{{ url('/mst/preventive/schedule/me_stamping') }}">ME Stamping</a>
                        <a class="nav-link" href="{{ url('/mst/preventive/schedule/power_house') }}">Power House</a>


                    </nav>
                </div>

                <!-- Sidenav Accordion (Master PM) -->
                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapsemaster" aria-expanded="false" aria-controls="collapsemaster">
                    <div class="nav-link-icon"><i class="fas fa-database"></i></div>
                    Master PM
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <!-- Nested Navigation for Master PM -->
                <div class="collapse" id="collapsemaster" data-bs-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ url('/mst/preventive') }}">PM Form</a>

                    </nav>
                </div>

                @if(\Auth::user()->role === 'IT')
                <!-- Sidenav Menu Heading (Configuration) -->
                <div class="sidenav-menu-heading">Configuration</div>

                <!-- Sidenav Accordion (Master Configuration) -->
                <a class="nav-link collapsed" href="javascript:void(0);" data-bs-toggle="collapse" data-bs-target="#collapseUtilities" aria-expanded="false" aria-controls="collapseUtilities">
                    <div class="nav-link-icon"><i data-feather="tool"></i></div>
                    Master Configuration
                    <div class="sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <!-- Nested Navigation for Master Configuration -->
                <div class="collapse" id="collapseUtilities" data-bs-parent="#accordionSidenav">
                    <nav class="sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ url('/dropdown') }}">Dropdown</a>
                        <a class="nav-link" href="{{ url('/rule') }}">Rules</a>
                        <a class="nav-link" href="{{ url('/user') }}">User</a>
                    </nav>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidenav Footer -->
        <div class="sidenav-footer">
            <div class="sidenav-footer-content">
                <div class="sidenav-footer-subtitle">Logged in as:</div>
                <div class="sidenav-footer-title">{{ auth()->user()->name }}</div>
            </div>
        </div>
    </nav>
</div>
