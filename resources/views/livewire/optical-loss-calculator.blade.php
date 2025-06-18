<div class="space-y-6">
    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button wire:click="setActiveTab('calculator')"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                               {{ $active_tab === 'calculator' ? 'border-blue-500 text-blue-600' : '' }}">
                    📊 Calculadora Manual
                </button>
                <button wire:click="setActiveTab('visual')"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                               {{ $active_tab === 'visual' ? 'border-blue-500 text-blue-600' : '' }}">
                    🎨 Editor Visual
                </button>
            </nav>
        </div>
    </div>

    <!-- Header with Results Summary -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Input Power -->
            <div class="text-center">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Potencia de Entrada</h3>
                <p class="mt-2 text-3xl font-bold text-blue-600">{{ number_format($input_power_dbm, 2) }} dBm</p>
            </div>
            
            <!-- Total Loss -->
            <div class="text-center">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pérdida Total</h3>
                <p class="mt-2 text-3xl font-bold text-orange-600">
                    {{ number_format($total_loss_db, 2) }} dB
                </p>
            </div>
            
            <!-- Output Power -->
            <div class="text-center">
                <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Potencia de Salida</h3>
                <p class="mt-2 text-3xl font-bold text-{{ $this->getOutputPowerColor() }}-600">
                    {{ number_format($output_power_dbm, 2) }} dBm
                </p>
            </div>
        </div>
        
        <!-- Warnings -->
        @if(!empty($warnings))
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Advertencias del Sistema</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($warnings as $warning)
                                    <li>{{ $warning }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Calculator Tab Content -->
    @if($active_tab === 'calculator')
        <!-- Main Calculator Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Input Parameters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Parámetros de Entrada</h2>
                <button wire:click="resetToDefaults" 
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Valores por Defecto
                </button>
            </div>

            <div class="space-y-6">
                <!-- Input Power -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Potencia de Entrada (dBm)
                    </label>
                    <input type="number" 
                           wire:model.live="input_power_dbm" 
                           step="0.1"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Splitters Section -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-gray-700">Splitters</label>
                        <button wire:click="addSplitter" 
                                class="text-sm bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700">
                            + Agregar Splitter
                        </button>
                    </div>
                    
                    @foreach($splitters as $index => $splitter)
                        <div class="border border-gray-200 rounded-md p-4 mb-3 last:mb-0">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700">Splitter {{ $index + 1 }}</span>
                                @if(count($splitters) > 1)
                                    <button wire:click="removeSplitter('{{ $splitter['id'] }}')" 
                                            class="text-red-600 hover:text-red-800">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Tipo</label>
                                    <select wire:model.live="splitters.{{ $index }}.type" 
                                            class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @foreach($splitter_types as $type => $loss)
                                            <option value="{{ $type }}">{{ $type }} ({{ $loss }} dB)</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Cantidad</label>
                                    <input type="number" 
                                           wire:model.live="splitters.{{ $index }}.count" 
                                           min="1"
                                           class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           wire:model.live="splitters.{{ $index }}.use_custom"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-xs text-gray-600">Usar pérdida personalizada</span>
                                </label>
                                
                                @if($splitter['use_custom'] ?? false)
                                    <input type="number" 
                                           wire:model.live="splitters.{{ $index }}.custom_loss" 
                                           step="0.1"
                                           placeholder="Pérdida en dB"
                                           class="mt-2 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Fusions -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad de Fusiones
                        </label>
                        <input type="number" 
                               wire:model.live="fusion_count" 
                               min="0"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pérdida por Fusión (dB)
                        </label>
                        <input type="number" 
                               wire:model.live="fusion_loss_per_unit" 
                               step="0.01" 
                               min="0"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Couplers -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad de Acopladores
                        </label>
                        <input type="number" 
                               wire:model.live="coupler_count" 
                               min="0"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pérdida por Acoplador (dB)
                        </label>
                        <input type="number" 
                               wire:model.live="coupler_loss_per_unit" 
                               step="0.1" 
                               min="0"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Fiber -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Distancia de Fibra (km)
                        </label>
                        <input type="number" 
                               wire:model.live="fiber_distance_km" 
                               step="0.1" 
                               min="0"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pérdida por km (dB/km)
                        </label>
                        <input type="number" 
                               wire:model.live="fiber_loss_per_km" 
                               step="0.01" 
                               min="0"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Design Margin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Margen de Diseño (dB)
                    </label>
                    <input type="number" 
                           wire:model.live="design_margin_db" 
                           step="0.1" 
                           min="0"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Results and Breakdown -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Resultados del Cálculo</h2>
                <button wire:click="toggleBreakdown" 
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    {{ $show_breakdown ? 'Ocultar' : 'Mostrar' }} Desglose
                </button>
            </div>

            @if($this->hasResults())
                <!-- Summary Cards -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500">Pérdida Total</h3>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ number_format($total_loss_db, 2) }} dB
                        </p>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500">Potencia Final</h3>
                        <p class="text-2xl font-bold text-{{ $this->getOutputPowerColor() }}-600 mt-1">
                            {{ number_format($output_power_dbm, 2) }} dBm
                        </p>
                    </div>
                </div>

                <!-- Detailed Breakdown -->
                @if($show_breakdown && !empty($breakdown))
                    <div class="space-y-3">
                        <h3 class="text-sm font-medium text-gray-700 border-b border-gray-200 pb-2">
                            Desglose Detallado
                        </h3>
                        
                        @foreach($breakdown as $item)
                            <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-md">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $item['component'] }}</span>
                                    @if(isset($item['quantity']))
                                        <span class="text-sm text-gray-500">
                                            ({{ $item['quantity'] }} × {{ number_format($item['loss_per_unit'], 2) }} dB)
                                        </span>
                                    @endif
                                    @if(isset($item['distance_km']))
                                        <span class="text-sm text-gray-500">
                                            ({{ $item['distance_km'] }} km × {{ $item['loss_per_km'] }} dB/km)
                                        </span>
                                    @endif
                                </div>
                                <span class="font-semibold text-gray-900">
                                    {{ number_format($item['total_loss'] ?? $item['value'] ?? 0, 2) }} dB
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Visual Network Diagram -->
                <div class="mt-8">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">Diagrama de Red</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-2 text-sm">
                            <div class="bg-green-500 text-white px-2 py-1 rounded">
                                OLT: {{ number_format($input_power_dbm, 1) }}dBm
                            </div>
                            <div class="flex-1 border-t-2 border-orange-400"></div>
                            @if($splitter_loss_total > 0)
                                <div class="bg-blue-500 text-white px-2 py-1 rounded text-xs">
                                    Splitters: -{{ number_format($splitter_loss_total, 1) }}dB
                                </div>
                                <div class="flex-1 border-t-2 border-orange-400"></div>
                            @endif
                            @if($fiber_loss_total > 0)
                                <div class="bg-purple-500 text-white px-2 py-1 rounded text-xs">
                                    Fibra: -{{ number_format($fiber_loss_total, 1) }}dB
                                </div>
                                <div class="flex-1 border-t-2 border-orange-400"></div>
                            @endif
                            <div class="bg-{{ $this->getOutputPowerColor() }}-500 text-white px-2 py-1 rounded">
                                ONT: {{ number_format($output_power_dbm, 1) }}dBm
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p class="mt-2">Ingrese los parámetros para ver el cálculo</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Visual Editor Tab Content -->
    @if($active_tab === 'visual')
        <div class="grid grid-cols-12 gap-6 h-screen">
            <!-- Component Palette -->
            <div class="col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Componentes</h3>
                <div class="space-y-3">
                    <!-- OLT Component -->
                    <div class="component-item bg-blue-100 border-2 border-blue-300 rounded-lg p-3 cursor-move hover:bg-blue-200 transition-colors"
                         draggable="true" 
                         data-component-type="olt"
                         ondragstart="handleDragStart(event)">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center">
                                <span class="text-white text-xs font-bold">OLT</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Terminal Óptica</span>
                        </div>
                    </div>

                    <!-- Splitter Component -->
                    <div class="component-item bg-green-100 border-2 border-green-300 rounded-lg p-3 cursor-move hover:bg-green-200 transition-colors"
                         draggable="true" 
                         data-component-type="splitter"
                         ondragstart="handleDragStart(event)">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center">
                                <span class="text-white text-xs font-bold">SPL</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Splitter</span>
                        </div>
                    </div>

                    <!-- Fusion Component -->
                    <div class="component-item bg-yellow-100 border-2 border-yellow-300 rounded-lg p-3 cursor-move hover:bg-yellow-200 transition-colors"
                         draggable="true" 
                         data-component-type="fusion"
                         ondragstart="handleDragStart(event)">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-yellow-500 rounded flex items-center justify-center">
                                <span class="text-white text-xs font-bold">FUS</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Fusión</span>
                        </div>
                    </div>

                    <!-- Coupler Component -->
                    <div class="component-item bg-orange-100 border-2 border-orange-300 rounded-lg p-3 cursor-move hover:bg-orange-200 transition-colors"
                         draggable="true" 
                         data-component-type="coupler"
                         ondragstart="handleDragStart(event)">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-orange-500 rounded flex items-center justify-center">
                                <span class="text-white text-xs font-bold">CPL</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Acoplador</span>
                        </div>
                    </div>

                    <!-- Fiber Component -->
                    <div class="component-item bg-purple-100 border-2 border-purple-300 rounded-lg p-3 cursor-move hover:bg-purple-200 transition-colors"
                         draggable="true" 
                         data-component-type="fiber"
                         ondragstart="handleDragStart(event)">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-purple-500 rounded flex items-center justify-center">
                                <span class="text-white text-xs font-bold">FIB</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Fibra Óptica</span>
                        </div>
                    </div>

                    <!-- ONT Component -->
                    <div class="component-item bg-red-100 border-2 border-red-300 rounded-lg p-3 cursor-move hover:bg-red-200 transition-colors"
                         draggable="true" 
                         data-component-type="ont"
                         ondragstart="handleDragStart(event)">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-red-500 rounded flex items-center justify-center">
                                <span class="text-white text-xs font-bold">ONT</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Terminal Red</span>
                        </div>
                    </div>
                </div>

                <!-- Connection Tools -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Herramientas</h4>
                    <div class="space-y-2">
                        <button id="connection-mode-btn" onclick="toggleConnectionMode()" 
                                class="w-full bg-green-600 text-white px-3 py-2 rounded-md hover:bg-green-700 transition-colors text-sm">
                            🔗 Modo Conexión
                        </button>
                        <button onclick="clearCanvas()" 
                                class="w-full bg-red-600 text-white px-3 py-2 rounded-md hover:bg-red-700 transition-colors text-sm">
                            🗑️ Limpiar
                        </button>
                        <button onclick="autoLayout()" 
                                class="w-full bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm">
                            ✨ Auto Layout
                        </button>
                        
                        <!-- Debug Info -->
                        <div class="mt-4 pt-2 border-t border-gray-200">
                            <div class="text-xs text-gray-500 space-y-1">
                                <div>Mouse: <span id="mouse-coords">--</span></div>
                                <div>Zoom: <span id="debug-zoom">100%</span></div>
                                <div>Pan: <span id="debug-pan">0, 0</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Canvas Area -->
            <div class="col-span-8 bg-white rounded-lg shadow-sm border border-gray-200 relative overflow-hidden">
                <!-- Canvas Header with Controls -->
                <div class="absolute top-4 left-4 z-20">
                    <h3 class="text-lg font-semibold text-gray-900">Área de Diseño</h3>
                    <p class="text-sm text-gray-500">Arrastra componentes aquí y conéctalos</p>
                    <div id="connection-status" class="hidden mt-2 px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm">
                        Modo Conexión Activo - Haz clic en dos componentes para conectar
                    </div>
                </div>

                <!-- Zoom Controls -->
                <div class="absolute top-4 right-4 z-20 bg-white rounded-lg shadow-md border border-gray-200 p-2">
                    <div class="flex flex-col space-y-2">
                        <div class="text-xs text-gray-600 text-center">Zoom: <span id="zoom-level">100%</span></div>
                        <button onclick="zoomIn()" class="p-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors" title="Zoom In">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                        <button onclick="zoomOut()" class="p-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors" title="Zoom Out">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <button onclick="zoomToFit()" class="p-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors" title="Zoom to Fit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/>
                            </svg>
                        </button>
                        <button onclick="resetZoom()" class="p-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors" title="Reset Zoom">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- SVG Canvas Container -->
                <div id="canvas-container" class="w-full h-full relative overflow-hidden">
                    <!-- SVG Canvas -->
                    <svg id="network-canvas" 
                         class="w-full h-full"
                         style="cursor: grab; height: 500px;"
                         ondrop="handleDrop(event)" 
                         ondragover="handleDragOver(event)"
                         onclick="handleCanvasClick(event)"
                         onwheel="handleWheel(event)"
                         onmousedown="handleCanvasMouseDown(event)"
                         viewBox="0 0 1200 800"
                         preserveAspectRatio="xMidYMid meet">
                        
                        <!-- Grid Pattern -->
                        <defs>
                            <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
                                <path d="M 20 0 L 0 0 0 20" fill="none" stroke="#f0f0f0" stroke-width="1"/>
                            </pattern>
                            <!-- Arrow marker -->
                            <marker id="arrowhead" markerWidth="10" markerHeight="7" 
                                    refX="9" refY="3.5" orient="auto">
                                <polygon points="0 0, 10 3.5, 0 7" fill="#666" />
                            </marker>
                        </defs>
                        
                        <!-- Background with grid -->
                        <rect width="100%" height="100%" fill="url(#grid)" />
                        
                        <!-- Main content group that will be transformed for zoom/pan -->
                        <g id="canvas-content">
                            <!-- Connections will be drawn here -->
                            <g id="connections"></g>
                            
                            <!-- Components will be placed here -->
                            <g id="components"></g>
                        </g>
                    </svg>
                </div>
            </div>

            <!-- Properties Panel -->
            <div class="col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Propiedades</h3>
                
                <div id="no-selection" class="text-center text-gray-500 py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.122 2.122"/>
                    </svg>
                    <p class="mt-2 text-sm">Selecciona un componente para editar sus propiedades</p>
                </div>

                <div id="component-properties" class="hidden space-y-4">
                    <!-- Properties will be dynamically populated here -->
                </div>

                <!-- Network Analysis -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Análisis de Red</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Componentes:</span>
                            <span id="component-count" class="font-medium">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Conexiones:</span>
                            <span id="connection-count" class="font-medium">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pérdida Total:</span>
                            <span id="visual-total-loss" class="font-medium">0.00 dB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Potencia Final:</span>
                            <span id="visual-output-power" class="font-medium">0.00 dBm</span>
                        </div>
                    </div>
                    
                    <!-- Path Analysis -->
                    <div class="mt-4">
                        <h5 class="text-xs font-semibold text-gray-700 mb-2">Rutas de Señal</h5>
                        <div id="signal-paths" class="space-y-1 text-xs">
                            <div class="text-gray-500">No hay rutas calculadas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Visual Editor JavaScript -->
@if($active_tab === 'visual')
<script>
// Global variables for the visual editor
let selectedComponent = null;
let isDragging = false;
let dragOffset = { x: 0, y: 0 };
let componentCounter = 0;
let connections = [];
let components = [];
let lastMouseDownTime = 0;
let dragThreshold = 5; // pixels
let dragStartPos = { x: 0, y: 0 };

// Connection mode variables
let connectionMode = false;
let firstComponentForConnection = null;
let connectionPoints = [];

// Zoom and Pan variables
let zoomScale = 1.0;
let panX = 0;
let panY = 0;
let isPanning = false;
let panStartPos = { x: 0, y: 0 };
let canvasViewBox = { x: 0, y: 0, width: 1200, height: 800 };

// Component templates with connection points
const componentTemplates = {
    olt: { 
        width: 80, height: 40, color: '#3b82f6', label: 'OLT', power: 5.0,
        connectionPoints: [
            { id: 'out', x: 80, y: 20, type: 'output', label: 'OUT' }
        ]
    },
    splitter: { 
        width: 80, height: 60, color: '#10b981', label: 'SPL', type: '1:2', loss: 3.2,
        connectionPoints: function(type) {
            const points = [{ id: 'in', x: 0, y: 30, type: 'input', label: 'IN' }];
            const outputs = parseInt(type.split(':')[1]) || 2;
            for (let i = 0; i < outputs; i++) {
                points.push({
                    id: `out${i + 1}`,
                    x: 80,
                    y: 15 + (i * 30 / Math.max(1, outputs - 1)),
                    type: 'output',
                    label: `OUT${i + 1}`
                });
            }
            return points;
        }
    },
    fusion: { 
        width: 50, height: 20, color: '#f59e0b', label: 'FUS', loss: 0.03,
        connectionPoints: [
            { id: 'in', x: 0, y: 10, type: 'input', label: 'IN' },
            { id: 'out', x: 50, y: 10, type: 'output', label: 'OUT' }
        ]
    },
    coupler: { 
        width: 60, height: 40, color: '#f97316', label: 'CPL', loss: 0.5,
        connectionPoints: [
            { id: 'in', x: 0, y: 20, type: 'input', label: 'IN' },
            { id: 'out', x: 60, y: 20, type: 'output', label: 'OUT' }
        ]
    },
    fiber: { 
        width: 120, height: 15, color: '#8b5cf6', label: 'FIB', distance: 750, lossPerKm: 0.2,
        connectionPoints: [
            { id: 'in', x: 0, y: 7.5, type: 'input', label: 'IN' },
            { id: 'out', x: 120, y: 7.5, type: 'output', label: 'OUT' }
        ]
    },
    ont: { 
        width: 80, height: 40, color: '#ef4444', label: 'ONT', sensitivity: -28,
        connectionPoints: [
            { id: 'in', x: 0, y: 20, type: 'input', label: 'IN' }
        ]
    }
};

// Splitter type configurations
const splitterTypes = {
    '1:2': { loss: 3.2, outputs: 2 },
    '1:4': { loss: 7.2, outputs: 4 },
    '1:8': { loss: 11.2, outputs: 8 },
    '1:16': { loss: 14.0, outputs: 16 },
    '1:32': { loss: 17.5, outputs: 32 },
    '1:64': { loss: 21.0, outputs: 64 }
};

// Drag and drop handlers
function handleDragStart(event) {
    event.dataTransfer.setData('text/plain', event.target.dataset.componentType);
}

function handleDragOver(event) {
    event.preventDefault();
}

function handleDrop(event) {
    event.preventDefault();
    console.log('Drop event triggered');
    
    const componentType = event.dataTransfer.getData('text/plain');
    if (!componentType) return;
    
    // Convert screen coordinates to SVG coordinates considering zoom/pan
    const svgCoords = screenToSVG(event.clientX, event.clientY);
    
    console.log('Dropping component:', componentType, 'at', svgCoords.x, svgCoords.y);
    
    createComponent(componentType, svgCoords.x, svgCoords.y);
}

// Create component on canvas
function createComponent(type, x, y) {
    const template = componentTemplates[type];
    if (!template) {
        console.error('Template no encontrado para tipo:', type);
        return;
    }
    
    const id = `component-${++componentCounter}`;
    console.log('Creando componente:', type, 'con ID:', id, 'en posición:', x, y);
    
    const component = {
        id: id,
        type: type,
        x: Math.max(0, x - template.width / 2),
        y: Math.max(0, y - template.height / 2),
        width: template.width,
        height: template.height,
        properties: { ...template },
        connectionPoints: getConnectionPoints(type, template.type || '1:2')
    };
    
    components.push(component);
    console.log('Componentes totales:', components.length);
    
    renderComponent(component);
    updateNetworkAnalysis();
    
    // Auto-select the new component
    setTimeout(() => {
        selectComponent(component);
    }, 100);
}

// Get connection points for component
function getConnectionPoints(type, splitterType = '1:2') {
    const template = componentTemplates[type];
    if (typeof template.connectionPoints === 'function') {
        return template.connectionPoints(splitterType);
    }
    return template.connectionPoints || [];
}

// Render component on SVG
function renderComponent(component) {
    const svg = document.getElementById('components');
    if (!svg) {
        console.error('SVG container no encontrado');
        return;
    }
    
    const template = component.properties;
    
    // Remove existing component if it exists
    const existingElement = document.getElementById(component.id);
    if (existingElement) {
        existingElement.remove();
    }
    
    const group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
    group.id = component.id;
    group.setAttribute('transform', `translate(${component.x}, ${component.y})`);
    group.style.cursor = 'move';
    group.setAttribute('data-component-type', component.type);
    
    console.log('Renderizando componente:', component.id, 'en posición:', component.x, component.y);
    
    // Component background
    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    rect.setAttribute('width', component.width);
    rect.setAttribute('height', component.height);
    rect.setAttribute('fill', template.color);
    rect.setAttribute('stroke', '#ffffff');
    rect.setAttribute('stroke-width', '2');
    rect.setAttribute('rx', '5');
    rect.style.filter = 'drop-shadow(2px 2px 4px rgba(0,0,0,0.1))';
    
    // Component label
    const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
    text.setAttribute('x', component.width / 2);
    text.setAttribute('y', component.height / 2 + 4);
    text.setAttribute('text-anchor', 'middle');
    text.setAttribute('fill', 'white');
    text.setAttribute('font-size', '12');
    text.setAttribute('font-weight', 'bold');
    text.setAttribute('pointer-events', 'none');
    text.textContent = template.label;
    
    // Add type info for splitters
    if (component.type === 'splitter') {
        const typeText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        typeText.setAttribute('x', component.width / 2);
        typeText.setAttribute('y', component.height / 2 + 18);
        typeText.setAttribute('text-anchor', 'middle');
        typeText.setAttribute('fill', 'white');
        typeText.setAttribute('font-size', '8');
        typeText.setAttribute('pointer-events', 'none');
        typeText.textContent = component.properties.type;
        group.appendChild(typeText);
    }
    
    // Add distance info for fiber
    if (component.type === 'fiber') {
        const distanceText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        distanceText.setAttribute('x', component.width / 2);
        distanceText.setAttribute('y', component.height + 15);
        distanceText.setAttribute('text-anchor', 'middle');
        distanceText.setAttribute('fill', '#666');
        distanceText.setAttribute('font-size', '8');
        distanceText.setAttribute('pointer-events', 'none');
        distanceText.textContent = `${component.properties.distance}m`;
        group.appendChild(distanceText);
    }
    
    // Add loss info for fusion and coupler
    if (component.type === 'fusion' || component.type === 'coupler') {
        const lossText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        lossText.setAttribute('x', component.width / 2);
        lossText.setAttribute('y', component.height + 15);
        lossText.setAttribute('text-anchor', 'middle');
        lossText.setAttribute('fill', '#666');
        lossText.setAttribute('font-size', '8');
        lossText.setAttribute('pointer-events', 'none');
        lossText.textContent = `-${component.properties.loss}dB`;
        group.appendChild(lossText);
    }
    
    group.appendChild(rect);
    group.appendChild(text);
    
    // Add connection points
    component.connectionPoints.forEach(point => {
        const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', point.x);
        circle.setAttribute('cy', point.y);
        circle.setAttribute('r', '4');
        circle.setAttribute('fill', point.type === 'input' ? '#dc2626' : '#16a34a');
        circle.setAttribute('stroke', '#ffffff');
        circle.setAttribute('stroke-width', '1');
        circle.style.cursor = 'pointer';
        circle.setAttribute('data-point-id', point.id);
        circle.setAttribute('data-component-id', component.id);
        circle.setAttribute('data-point-type', point.type);
        
        // Connection point click handler
        circle.addEventListener('click', (e) => {
            e.stopPropagation();
            handleConnectionPointClick(component, point, e);
        });
        
        group.appendChild(circle);
        
        // Add point label
        const pointLabel = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        pointLabel.setAttribute('x', point.x + (point.type === 'input' ? -10 : 10));
        pointLabel.setAttribute('y', point.y - 8);
        pointLabel.setAttribute('text-anchor', point.type === 'input' ? 'end' : 'start');
        pointLabel.setAttribute('fill', '#666');
        pointLabel.setAttribute('font-size', '8');
        pointLabel.setAttribute('pointer-events', 'none');
        pointLabel.textContent = point.label;
        group.appendChild(pointLabel);
    });
    
    // Add event listeners
    group.addEventListener('mousedown', (e) => handleComponentMouseDown(e, component));
    group.addEventListener('click', (e) => handleComponentClick(e, component));
    
    // Prevent context menu and drag from browser
    group.addEventListener('contextmenu', (e) => e.preventDefault());
    group.addEventListener('dragstart', (e) => e.preventDefault());
    
    svg.appendChild(group);
    
    console.log('Componente renderizado exitosamente:', component.id);
    
    // Disable Livewire updates temporarily to prevent DOM manipulation
    if (window.Livewire) {
        window.Livewire.stop();
    }
    
    // Protect against removeChild calls
    const originalRemoveChild = svg.removeChild;
    svg.removeChild = function(child) {
        if (child.id && child.id.startsWith('component-')) {
            console.error('🚨 INTENTO DE ELIMINAR COMPONENTE BLOQUEADO:', child.id);
            console.trace('Stack trace:');
            return child; // Return the child without removing it
        }
        return originalRemoveChild.call(this, child);
    };
}

// Handle connection point clicks
function handleConnectionPointClick(component, point, event) {
    if (!connectionMode) return;
    
    console.log('Punto de conexión clickeado:', component.id, point.id);
    
    if (!firstComponentForConnection) {
        // First component selected
        firstComponentForConnection = { component, point };
        highlightConnectionPoint(component, point, true);
        console.log('Primer componente seleccionado para conexión');
    } else {
        // Second component selected - create connection
        const firstComp = firstComponentForConnection.component;
        const firstPoint = firstComponentForConnection.point;
        
        // Check if connection is valid
        if (firstComp.id === component.id) {
            console.log('No se puede conectar un componente consigo mismo');
            return;
        }
        
        if (firstPoint.type === point.type) {
            console.log('No se puede conectar puntos del mismo tipo');
            return;
        }
        
        // Create connection
        createConnection(
            firstComp, firstPoint,
            component, point
        );
        
        // Reset connection mode
        highlightConnectionPoint(firstComp, firstPoint, false);
        firstComponentForConnection = null;
        
        console.log('Conexión creada entre', firstComp.id, 'y', component.id);
    }
}

// Create connection between components
function createConnection(fromComp, fromPoint, toComp, toPoint) {
    const connectionId = `connection-${fromComp.id}-${fromPoint.id}-${toComp.id}-${toPoint.id}`;
    
    // Check if connection already exists
    if (connections.find(c => c.id === connectionId)) {
        console.log('Conexión ya existe');
        return;
    }
    
    const connection = {
        id: connectionId,
        from: { component: fromComp, point: fromPoint },
        to: { component: toComp, point: toPoint }
    };
    
    connections.push(connection);
    renderConnection(connection);
    updateNetworkAnalysis();
    calculateSignalPaths();
}

// Render connection on SVG
function renderConnection(connection) {
    const svg = document.getElementById('connections');
    
    const fromComp = connection.from.component;
    const fromPoint = connection.from.point;
    const toComp = connection.to.component;
    const toPoint = connection.to.point;
    
    const x1 = fromComp.x + fromPoint.x;
    const y1 = fromComp.y + fromPoint.y;
    const x2 = toComp.x + toPoint.x;
    const y2 = toComp.y + toPoint.y;
    
    // Create path with arrow
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    const d = `M ${x1} ${y1} L ${x2} ${y2}`;
    path.setAttribute('d', d);
    path.setAttribute('stroke', '#666');
    path.setAttribute('stroke-width', '2');
    path.setAttribute('fill', 'none');
    path.setAttribute('marker-end', 'url(#arrowhead)');
    path.id = connection.id;
    
    svg.appendChild(path);
}

// Update all connections when components move
function updateConnections() {
    connections.forEach(connection => {
        const element = document.getElementById(connection.id);
        if (element) {
            const fromComp = connection.from.component;
            const fromPoint = connection.from.point;
            const toComp = connection.to.component;
            const toPoint = connection.to.point;
            
            const x1 = fromComp.x + fromPoint.x;
            const y1 = fromComp.y + fromPoint.y;
            const x2 = toComp.x + toPoint.x;
            const y2 = toComp.y + toPoint.y;
            
            const d = `M ${x1} ${y1} L ${x2} ${y2}`;
            element.setAttribute('d', d);
        }
    });
}

// Highlight connection point
function highlightConnectionPoint(component, point, highlight) {
    const element = document.getElementById(component.id);
    if (element) {
        const circle = element.querySelector(`[data-point-id="${point.id}"]`);
        if (circle) {
            circle.setAttribute('r', highlight ? '6' : '4');
            circle.setAttribute('stroke-width', highlight ? '2' : '1');
        }
    }
}

// Toggle connection mode
function toggleConnectionMode() {
    connectionMode = !connectionMode;
    const btn = document.getElementById('connection-mode-btn');
    const status = document.getElementById('connection-status');
    
    if (connectionMode) {
        btn.textContent = '🔓 Salir Conexión';
        btn.classList.remove('bg-green-600', 'hover:bg-green-700');
        btn.classList.add('bg-red-600', 'hover:bg-red-700');
        status.classList.remove('hidden');
    } else {
        btn.textContent = '🔗 Modo Conexión';
        btn.classList.remove('bg-red-600', 'hover:bg-red-700');
        btn.classList.add('bg-green-600', 'hover:bg-green-700');
        status.classList.add('hidden');
        
        // Reset connection state
        if (firstComponentForConnection) {
            highlightConnectionPoint(
                firstComponentForConnection.component,
                firstComponentForConnection.point,
                false
            );
            firstComponentForConnection = null;
        }
    }
}

// Component interaction handlers
function handleComponentMouseDown(event, component) {
    if (connectionMode || isPanning) return; // Don't drag in connection mode or while panning
    
    event.preventDefault();
    event.stopPropagation();
    
    lastMouseDownTime = Date.now();
    isDragging = false;
    selectedComponent = component;
    
    // Convert screen coordinates to SVG coordinates
    const svgCoords = screenToSVG(event.clientX, event.clientY);
    
    dragStartPos.x = event.clientX;
    dragStartPos.y = event.clientY;
    
    // Calculate drag offset in SVG coordinates
    dragOffset.x = svgCoords.x - component.x;
    dragOffset.y = svgCoords.y - component.y;
    
    document.addEventListener('mousemove', handleMouseMove);
    document.addEventListener('mouseup', handleMouseUp);
}

function handleComponentClick(event, component) {
    if (connectionMode) return; // Don't select in connection mode
    
    event.stopPropagation();
    selectComponent(component);
}

function handleMouseMove(event) {
    if (!selectedComponent || connectionMode || isPanning) return;
    
    // Check if we should start dragging
    if (!isDragging) {
        const dragDistance = Math.sqrt(
            Math.pow(event.clientX - dragStartPos.x, 2) +
            Math.pow(event.clientY - dragStartPos.y, 2)
        );
        
        if (dragDistance > dragThreshold) {
            isDragging = true;
        } else {
            return;
        }
    }
    
    event.preventDefault();
    
    // Convert screen coordinates to SVG coordinates considering zoom/pan
    const svgCoords = screenToSVG(event.clientX, event.clientY);
    
    selectedComponent.x = svgCoords.x - dragOffset.x;
    selectedComponent.y = svgCoords.y - dragOffset.y;
    
    // Constrain to canvas bounds (SVG coordinates)
    selectedComponent.x = Math.max(0, Math.min(selectedComponent.x, canvasViewBox.width - selectedComponent.width));
    selectedComponent.y = Math.max(0, Math.min(selectedComponent.y, canvasViewBox.height - selectedComponent.height));
    
    const element = document.getElementById(selectedComponent.id);
    if (element) {
        element.setAttribute('transform', `translate(${selectedComponent.x}, ${selectedComponent.y})`);
    }
    
    updateConnections();
}

function handleMouseUp(event) {
    if (isDragging) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    isDragging = false;
    document.removeEventListener('mousemove', handleMouseMove);
    document.removeEventListener('mouseup', handleMouseUp);
}

// Component selection
function selectComponent(component) {
    if (!component) return;
    
    selectedComponent = component;
    console.log('Seleccionando componente:', component.id);
    
    showComponentProperties(component);
    
    // Visual feedback - clear all selections first
    document.querySelectorAll('#components g rect').forEach(rect => {
        rect.setAttribute('stroke', '#ffffff');
        rect.setAttribute('stroke-width', '2');
    });
    
    // Highlight selected component
    const element = document.getElementById(component.id);
    if (element) {
        const rect = element.querySelector('rect');
        if (rect) {
            rect.setAttribute('stroke', '#fbbf24');
            rect.setAttribute('stroke-width', '3');
        }
    }
}

// Show component properties panel
function showComponentProperties(component) {
    document.getElementById('no-selection').classList.add('hidden');
    const panel = document.getElementById('component-properties');
    panel.classList.remove('hidden');
    
    panel.innerHTML = `
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
            <input type="text" value="${component.type.toUpperCase()}" disabled 
                   class="w-full rounded-md border-gray-300 bg-gray-50 text-sm">
        </div>
        ${generatePropertyInputs(component)}
        <button onclick="deleteComponent('${component.id}')" 
                class="w-full bg-red-600 text-white px-3 py-2 rounded-md hover:bg-red-700 transition-colors text-sm">
            🗑️ Eliminar
        </button>
    `;
}

// Generate property inputs based on component type
function generatePropertyInputs(component) {
    const props = component.properties;
    let inputs = '';
    
    switch (component.type) {
        case 'olt':
            inputs = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Potencia (dBm)</label>
                    <input type="number" step="0.1" value="${props.power}" 
                           onchange="updateComponentProperty('${component.id}', 'power', this.value)"
                           class="w-full rounded-md border-gray-300 text-sm">
                </div>
            `;
            break;
        case 'splitter':
            inputs = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <select onchange="updateSplitterType('${component.id}', this.value)"
                            class="w-full rounded-md border-gray-300 text-sm">
                        <option value="1:2" ${props.type === '1:2' ? 'selected' : ''}>1:2 (3.2 dB)</option>
                        <option value="1:4" ${props.type === '1:4' ? 'selected' : ''}>1:4 (7.2 dB)</option>
                        <option value="1:8" ${props.type === '1:8' ? 'selected' : ''}>1:8 (11.2 dB)</option>
                        <option value="1:16" ${props.type === '1:16' ? 'selected' : ''}>1:16 (14.0 dB)</option>
                        <option value="1:32" ${props.type === '1:32' ? 'selected' : ''}>1:32 (17.5 dB)</option>
                        <option value="1:64" ${props.type === '1:64' ? 'selected' : ''}>1:64 (21.0 dB)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pérdida Personalizada (dB)</label>
                    <input type="number" step="0.1" value="${props.customLoss || ''}" 
                           placeholder="Opcional"
                           onchange="updateComponentProperty('${component.id}', 'customLoss', this.value)"
                           class="w-full rounded-md border-gray-300 text-sm">
                </div>
            `;
            break;
        case 'fusion':
            inputs = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pérdida (dB)</label>
                    <input type="number" step="0.01" value="${props.loss}" 
                           onchange="updateComponentProperty('${component.id}', 'loss', this.value)"
                           class="w-full rounded-md border-gray-300 text-sm">
                </div>
            `;
            break;
        case 'coupler':
            inputs = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pérdida (dB)</label>
                    <input type="number" step="0.1" value="${props.loss}" 
                           onchange="updateComponentProperty('${component.id}', 'loss', this.value)"
                           class="w-full rounded-md border-gray-300 text-sm">
                </div>
            `;
            break;
        case 'fiber':
            inputs = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Distancia (metros)</label>
                    <input type="number" step="10" value="${props.distance}" 
                           onchange="updateComponentProperty('${component.id}', 'distance', this.value)"
                           class="w-full rounded-md border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pérdida por km (dB/km)</label>
                    <input type="number" step="0.01" value="${props.lossPerKm}" 
                           onchange="updateComponentProperty('${component.id}', 'lossPerKm', this.value)"
                           class="w-full rounded-md border-gray-300 text-sm" readonly>
                </div>
            `;
            break;
        case 'ont':
            inputs = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sensibilidad (dBm)</label>
                    <input type="number" step="0.1" value="${props.sensitivity}" 
                           onchange="updateComponentProperty('${component.id}', 'sensitivity', this.value)"
                           class="w-full rounded-md border-gray-300 text-sm">
                </div>
            `;
            break;
    }
    
    return inputs;
}

// Update component property
function updateComponentProperty(componentId, property, value) {
    const component = components.find(c => c.id === componentId);
    if (component) {
        component.properties[property] = parseFloat(value) || value;
        console.log('Propiedad actualizada:', componentId, property, value);
        updateNetworkAnalysis();
        calculateSignalPaths();
    }
}

// Update splitter type and regenerate connection points
function updateSplitterType(componentId, newType) {
    const component = components.find(c => c.id === componentId);
    if (component && component.type === 'splitter') {
        // Update properties
        component.properties.type = newType;
        component.properties.loss = splitterTypes[newType]?.loss || 3.2;
        
        // Remove existing connections to/from this component
        connections = connections.filter(conn => 
            conn.from.component.id !== componentId && 
            conn.to.component.id !== componentId
        );
        
        // Update connection points
        component.connectionPoints = getConnectionPoints('splitter', newType);
        
        // Re-render component
        renderComponent(component);
        updateConnectionsDisplay();
        updateNetworkAnalysis();
        calculateSignalPaths();
        
        // Update properties panel
        showComponentProperties(component);
    }
}

// Delete component
function deleteComponent(componentId) {
    if (confirm('¿Estás seguro de que quieres eliminar este componente?')) {
        // Remove connections
        connections = connections.filter(conn => 
            conn.from.component.id !== componentId && 
            conn.to.component.id !== componentId
        );
        
        // Remove component from array
        components = components.filter(c => c.id !== componentId);
        
        // Remove from DOM
        const element = document.getElementById(componentId);
        if (element) {
            element.remove();
        }
        
        // Clear properties panel
        document.getElementById('component-properties').classList.add('hidden');
        document.getElementById('no-selection').classList.remove('hidden');
        selectedComponent = null;
        
        updateConnectionsDisplay();
        updateNetworkAnalysis();
        calculateSignalPaths();
    }
}

// Update connections display after component deletion
function updateConnectionsDisplay() {
    const svg = document.getElementById('connections');
    svg.innerHTML = ''; // Clear all connections
    
    // Re-render remaining connections
    connections.forEach(connection => {
        renderConnection(connection);
    });
}

// Update network analysis panel
function updateNetworkAnalysis() {
    document.getElementById('component-count').textContent = components.length;
    document.getElementById('connection-count').textContent = connections.length;
}

// Calculate signal paths and losses
function calculateSignalPaths() {
    const oltComponents = components.filter(c => c.type === 'olt');
    const ontComponents = components.filter(c => c.type === 'ont');
    
    const paths = [];
    let totalLoss = 0;
    let outputPower = 0;
    
    if (oltComponents.length > 0 && ontComponents.length > 0) {
        // Find paths from each OLT to each ONT
        oltComponents.forEach(olt => {
            ontComponents.forEach(ont => {
                const path = findPath(olt, ont);
                if (path.length > 0) {
                    const pathLoss = calculatePathLoss(path);
                    const pathOutputPower = olt.properties.power - pathLoss;
                    
                    paths.push({
                        from: olt.id,
                        to: ont.id,
                        components: path,
                        loss: pathLoss,
                        outputPower: pathOutputPower
                    });
                    
                    // Use the worst case (highest loss) for display
                    if (pathLoss > totalLoss) {
                        totalLoss = pathLoss;
                        outputPower = pathOutputPower;
                    }
                }
            });
        });
    }
    
    // Update display
    document.getElementById('visual-total-loss').textContent = totalLoss.toFixed(2) + ' dB';
    document.getElementById('visual-output-power').textContent = outputPower.toFixed(2) + ' dBm';
    
    // Color code output power
    const outputElement = document.getElementById('visual-output-power');
    outputElement.className = 'font-medium';
    if (outputPower < -28) {
        outputElement.classList.add('text-red-600');
    } else if (outputPower < -25) {
        outputElement.classList.add('text-yellow-600');
    } else if (outputPower <= -8) {
        outputElement.classList.add('text-green-600');
    } else {
        outputElement.classList.add('text-red-600');
    }
    
    displaySignalPaths(paths);
}

// Find path between two components using DFS
function findPath(startComponent, endComponent) {
    const visited = new Set();
    const path = [];
    
    function dfs(component) {
        if (visited.has(component.id)) return false;
        if (component.id === endComponent.id) return true;
        
        visited.add(component.id);
        path.push(component);
        
        // Find connections from this component
        const outgoingConnections = connections.filter(conn => 
            conn.from.component.id === component.id
        );
        
        for (const connection of outgoingConnections) {
            if (dfs(connection.to.component)) {
                return true;
            }
        }
        
        path.pop();
        return false;
    }
    
    if (dfs(startComponent)) {
        path.push(endComponent);
        return path;
    }
    
    return [];
}

// Calculate loss for a given path
function calculatePathLoss(path) {
    let totalLoss = 0;
    
    path.forEach((component, index) => {
        if (index === 0) return; // Skip OLT (no loss)
        
        switch (component.type) {
            case 'splitter':
                const customLoss = component.properties.customLoss;
                totalLoss += customLoss || component.properties.loss;
                break;
            case 'fusion':
                totalLoss += component.properties.loss;
                break;
            case 'coupler':
                totalLoss += component.properties.loss;
                break;
            case 'fiber':
                // Convertir metros a kilómetros y calcular pérdida
                const distanceKm = component.properties.distance / 1000;
                totalLoss += distanceKm * component.properties.lossPerKm;
                break;
        }
    });
    
    return totalLoss;
}

// Calculate detailed breakdown for a path
function calculatePathBreakdown(path) {
    const breakdown = {
        oltPower: 0,
        components: []
    };
    
    path.forEach((component, index) => {
        if (component.type === 'olt') {
            breakdown.oltPower = component.properties.power;
            return;
        }
        
        let loss = 0;
        let name = '';
        
        switch (component.type) {
            case 'splitter':
                const customLoss = component.properties.customLoss;
                loss = customLoss || component.properties.loss;
                name = `Splitter ${component.properties.type}`;
                break;
            case 'fusion':
                loss = component.properties.loss;
                name = 'Fusión';
                break;
            case 'coupler':
                loss = component.properties.loss;
                name = 'Acoplador';
                break;
            case 'fiber':
                const distanceKm = component.properties.distance / 1000;
                loss = distanceKm * component.properties.lossPerKm;
                name = `Fibra ${component.properties.distance}m`;
                break;
        }
        
        if (loss > 0) {
            breakdown.components.push({ name, loss });
        }
    });
    
    return breakdown;
}

// Display signal paths in the analysis panel
function displaySignalPaths(paths) {
    const pathsContainer = document.getElementById('signal-paths');
    
    if (paths.length === 0) {
        pathsContainer.innerHTML = '<div class="text-gray-500">No hay rutas calculadas</div>';
        return;
    }
    
    pathsContainer.innerHTML = paths.map((path, index) => {
        const breakdown = calculatePathBreakdown(path.components);
        return `
            <div class="bg-gray-50 rounded p-3 border">
                <div class="font-medium text-xs mb-2">Ruta ${index + 1}: ${path.from} → ${path.to}</div>
                
                <div class="text-xs space-y-1">
                    <div class="flex justify-between">
                        <span>OLT:</span>
                        <span class="text-green-600">+${breakdown.oltPower} dBm</span>
                    </div>
                    ${breakdown.components.map(comp => `
                        <div class="flex justify-between">
                            <span>${comp.name}:</span>
                            <span class="text-red-600">-${comp.loss.toFixed(3)} dB</span>
                        </div>
                    `).join('')}
                    <div class="border-t pt-1 mt-1 flex justify-between font-medium">
                        <span>Total:</span>
                        <span class="${path.outputPower < -28 ? 'text-red-600' : path.outputPower < -25 ? 'text-yellow-600' : 'text-green-600'}">
                            ${path.outputPower.toFixed(2)} dBm
                        </span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Clear canvas
function clearCanvas() {
    if (confirm('¿Estás seguro de que quieres limpiar todo el diseño?')) {
        // Clear arrays
        components = [];
        connections = [];
        selectedComponent = null;
        firstComponentForConnection = null;
        
        // Clear SVG
        document.getElementById('components').innerHTML = '';
        document.getElementById('connections').innerHTML = '';
        
        // Clear properties panel
        document.getElementById('component-properties').classList.add('hidden');
        document.getElementById('no-selection').classList.remove('hidden');
        
        // Reset connection mode
        if (connectionMode) {
            toggleConnectionMode();
        }
        
        // Update analysis
        updateNetworkAnalysis();
        calculateSignalPaths();
    }
}

// Auto layout function
function autoLayout() {
    if (components.length === 0) return;
    
    // Simple horizontal layout
    const canvas = document.getElementById('network-canvas');
    const rect = canvas.getBoundingClientRect();
    const spacing = 150;
    const startX = 50;
    const centerY = rect.height / 2;
    
    components.forEach((component, index) => {
        component.x = startX + (index * spacing);
        component.y = centerY - component.height / 2;
        
        const element = document.getElementById(component.id);
        if (element) {
            element.setAttribute('transform', `translate(${component.x}, ${component.y})`);
        }
    });
    
    updateConnections();
}

// Handle canvas clicks (deselect components)
function handleCanvasClick(event) {
    if (connectionMode) return;
    
    // Only deselect if clicking on the canvas itself, not on components
    if (event.target.id === 'network-canvas' || event.target.tagName === 'rect' && event.target.getAttribute('fill') === 'url(#grid)') {
        selectedComponent = null;
        
        // Clear visual selection
        document.querySelectorAll('#components g rect').forEach(rect => {
            rect.setAttribute('stroke', '#ffffff');
            rect.setAttribute('stroke-width', '2');
        });
        
        // Hide properties panel
        document.getElementById('component-properties').classList.add('hidden');
        document.getElementById('no-selection').classList.remove('hidden');
    }
}

// Global debug function
window.debugComponents = function() {
    console.log('=== DEBUG INFO ===');
    console.log('Components:', components.length);
    console.log('Connections:', connections.length);
    console.log('Components array:', components);
    console.log('Connections array:', connections);
    console.log('DOM components:', document.querySelectorAll('#components g').length);
    console.log('DOM connections:', document.querySelectorAll('#connections path').length);
}

// ========== ZOOM AND PAN FUNCTIONALITY ==========

// Convert screen coordinates to SVG coordinates
function screenToSVG(screenX, screenY) {
    const canvas = document.getElementById('network-canvas');
    const rect = canvas.getBoundingClientRect();
    
    // Convert to normalized coordinates (0-1)
    const normalizedX = (screenX - rect.left) / rect.width;
    const normalizedY = (screenY - rect.top) / rect.height;
    
    // Convert to SVG coordinates
    const svgX = normalizedX * canvasViewBox.width;
    const svgY = normalizedY * canvasViewBox.height;
    
    // Account for zoom and pan transforms
    const transformedX = (svgX / zoomScale) - panX;
    const transformedY = (svgY / zoomScale) - panY;
    
    return { x: transformedX, y: transformedY };
}

// Update the canvas transform
function updateCanvasTransform() {
    const content = document.getElementById('canvas-content');
    if (content) {
        content.setAttribute('transform', `scale(${zoomScale}) translate(${panX}, ${panY})`);
    }
    
    // Update zoom level display
    document.getElementById('zoom-level').textContent = Math.round(zoomScale * 100) + '%';
    
    // Update debug info
    const debugZoom = document.getElementById('debug-zoom');
    const debugPan = document.getElementById('debug-pan');
    if (debugZoom) debugZoom.textContent = Math.round(zoomScale * 100) + '%';
    if (debugPan) debugPan.textContent = `${Math.round(panX)}, ${Math.round(panY)}`;
}

// Update cursor based on current mode
function updateCanvasCursor() {
    const canvas = document.getElementById('network-canvas');
    if (!canvas) return;
    
    if (connectionMode) {
        canvas.style.cursor = 'crosshair';
    } else if (isPanning) {
        canvas.style.cursor = 'grabbing';
    } else if (selectedComponent && isDragging) {
        canvas.style.cursor = 'move';
    } else {
        canvas.style.cursor = 'grab';
    }
}

// Handle mouse wheel for zooming
function handleWheel(event) {
    event.preventDefault();
    
    const zoomFactor = 0.1;
    const rect = event.currentTarget.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const mouseY = event.clientY - rect.top;
    
    // Calculate zoom center in SVG coordinates
    const svgPoint = screenToSVG(event.clientX, event.clientY);
    
    if (event.deltaY < 0) {
        // Zoom in
        zoomScale = Math.min(zoomScale * (1 + zoomFactor), 3.0);
    } else {
        // Zoom out
        zoomScale = Math.max(zoomScale * (1 - zoomFactor), 0.1);
    }
    
    updateCanvasTransform();
}

// Canvas mouse down - start panning or component interaction
function handleCanvasMouseDown(event) {
    if (connectionMode || selectedComponent || isDragging) {
        return; // Don't pan in connection mode or when dragging components
    }
    
    // Check if we clicked on empty space
    if (event.target.id === 'network-canvas' || 
        (event.target.tagName === 'rect' && event.target.getAttribute('fill') === 'url(#grid)')) {
        isPanning = true;
        panStartPos.x = event.clientX;
        panStartPos.y = event.clientY;
        
        event.preventDefault();
        
        document.addEventListener('mousemove', handleCanvasPan);
        document.addEventListener('mouseup', handleCanvasPanEnd);
        updateCanvasCursor();
    }
}

// Handle canvas panning
function handleCanvasPan(event) {
    if (!isPanning) return;
    
    const deltaX = (event.clientX - panStartPos.x) / zoomScale;
    const deltaY = (event.clientY - panStartPos.y) / zoomScale;
    
    panX += deltaX;
    panY += deltaY;
    
    panStartPos.x = event.clientX;
    panStartPos.y = event.clientY;
    
    updateCanvasTransform();
}

// End canvas panning
function handleCanvasPanEnd(event) {
    isPanning = false;
    document.removeEventListener('mousemove', handleCanvasPan);
    document.removeEventListener('mouseup', handleCanvasPanEnd);
    updateCanvasCursor();
}

// Zoom controls
function zoomIn() {
    zoomScale = Math.min(zoomScale * 1.2, 3.0);
    updateCanvasTransform();
}

function zoomOut() {
    zoomScale = Math.max(zoomScale / 1.2, 0.1);
    updateCanvasTransform();
}

function resetZoom() {
    zoomScale = 1.0;
    panX = 0;
    panY = 0;
    updateCanvasTransform();
}

function zoomToFit() {
    if (components.length === 0) {
        resetZoom();
        return;
    }
    
    // Calculate bounding box of all components
    let minX = Infinity, minY = Infinity;
    let maxX = -Infinity, maxY = -Infinity;
    
    components.forEach(component => {
        minX = Math.min(minX, component.x);
        minY = Math.min(minY, component.y);
        maxX = Math.max(maxX, component.x + component.width);
        maxY = Math.max(maxY, component.y + component.height);
    });
    
    // Add padding
    const padding = 50;
    minX -= padding;
    minY -= padding;
    maxX += padding;
    maxY += padding;
    
    // Calculate scale to fit
    const contentWidth = maxX - minX;
    const contentHeight = maxY - minY;
    const scaleX = canvasViewBox.width / contentWidth;
    const scaleY = canvasViewBox.height / contentHeight;
    
    zoomScale = Math.min(scaleX, scaleY, 2.0);
    
    // Center the content
    panX = (canvasViewBox.width / zoomScale - contentWidth) / 2 - minX / zoomScale;
    panY = (canvasViewBox.height / zoomScale - contentHeight) / 2 - minY / zoomScale;
    
    updateCanvasTransform();
}

// Initialize the visual editor when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('network-canvas')) {
        console.log('Editor visual inicializado');
        updateNetworkAnalysis();
        calculateSignalPaths();
        updateCanvasTransform(); // Initialize zoom/pan
        updateCanvasCursor(); // Initialize cursor
        
        // Add mouse tracking for debug info
        const canvas = document.getElementById('network-canvas');
        canvas.addEventListener('mousemove', function(event) {
            const svgCoords = screenToSVG(event.clientX, event.clientY);
            const mouseDisplay = document.getElementById('mouse-coords');
            if (mouseDisplay) {
                mouseDisplay.textContent = `${Math.round(svgCoords.x)}, ${Math.round(svgCoords.y)}`;
            }
        });
    }
});
</script>
@endif