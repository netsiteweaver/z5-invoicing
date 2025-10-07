{{--
    Action Button Reference Guide
    
    This component provides a consistent button system across the application.
    Each button type has predefined colors and icons.
    
    ============================================================================
    USAGE EXAMPLES
    ============================================================================
    
    1. BASIC USAGE (Link Button):
    <x-action-button type="edit" :href="route('customers.edit', $customer)" />
    
    2. DELETE BUTTON (with form):
    <x-action-button type="delete" :form-action="route('customers.destroy', $customer)" />
    
    3. REGULAR BUTTON (submit, reset, etc):
    <x-action-button type="save" />
    <x-action-button type="cancel" />
    
    4. CUSTOM LABEL:
    <x-action-button type="edit" :href="route('customers.edit', $customer)">
        Update Customer
    </x-action-button>
    
    5. CUSTOM COLOR AND ICON:
    <x-action-button type="custom" color="bg-purple-600 hover:bg-purple-700 focus:ring-purple-500" icon="fa-star">
        Special Action
    </x-action-button>
    
    6. DIFFERENT SIZES:
    <x-action-button type="edit" size="sm" :href="route('customers.edit', $customer)" />
    <x-action-button type="edit" size="md" :href="route('customers.edit', $customer)" />
    <x-action-button type="edit" size="lg" :href="route('customers.edit', $customer)" />
    
    7. ICON ONLY:
    <x-action-button type="edit" :href="route('customers.edit', $customer)" :icon-only="true" />
    
    8. WITH CUSTOM ATTRIBUTES:
    <x-action-button type="save" id="save-btn" x-on:click="submitForm()" />
    
    9. CUSTOM CONFIRMATION MESSAGE:
    <x-action-button 
        type="delete" 
        :form-action="route('customers.destroy', $customer)"
        confirm-message="Are you sure you want to delete this customer? All related data will be lost."
    />
    
    10. DISABLE CONFIRMATION:
    <x-action-button type="delete" :form-action="route('customers.destroy', $customer)" :confirm="false" />
    
    ============================================================================
    AVAILABLE BUTTON TYPES
    ============================================================================
    
    Type        | Color      | Icon                  | Default Label
    ------------|------------|----------------------|---------------
    create      | Emerald    | fa-plus              | Create
    edit        | Amber      | fa-pen               | Edit
    delete      | Rose       | fa-trash             | Delete
    view        | Sky        | fa-eye               | View
    save        | Blue       | fa-floppy-disk       | Save
    cancel      | Gray       | fa-xmark             | Cancel
    back        | Gray       | fa-arrow-left        | Back
    print       | Purple     | fa-print             | Print
    export      | Teal       | fa-file-export       | Export
    filter      | Indigo     | fa-filter            | Filter
    search      | Blue       | fa-magnifying-glass  | Search
    approve     | Green      | fa-check             | Approve
    reject      | Red        | fa-ban               | Reject
    submit      | Blue       | fa-paper-plane       | Submit
    download    | Green      | fa-download          | Download
    upload      | Blue       | fa-upload            | Upload
    send        | Cyan       | fa-envelope          | Send
    copy        | Slate      | fa-copy              | Copy
    share       | Violet     | fa-share-nodes       | Share
    refresh     | Blue       | fa-rotate            | Refresh
    add         | Emerald    | fa-plus              | Add
    remove      | Rose       | fa-minus             | Remove
    reset       | Orange     | fa-arrow-rotate-left | Reset
    settings    | Gray       | fa-gear              | Settings
    
    ============================================================================
    PARAMETERS
    ============================================================================
    
    @param string $type - Button type (see table above)
    @param string|null $href - URL for link-style button
    @param string|null $formAction - Form action URL for delete/submit buttons
    @param string|null $color - Custom color classes (for type="custom")
    @param string|null $icon - Custom icon class (for type="custom" or override)
    @param string|null $label - Custom label (overrides default)
    @param bool $confirm - Show confirmation dialog (default: true for delete)
    @param string|null $confirmMessage - Custom confirmation message
    @param string $size - Button size: sm, md (default), lg
    @param bool $iconOnly - Show only icon without text (default: false)
    
    ============================================================================
    MIGRATION GUIDE - OLD vs NEW
    ============================================================================
    
    OLD WAY:
    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-edit">
        <i class="btn-icon fa-solid fa-pen"></i>
        Edit
    </a>
    
    NEW WAY:
    <x-action-button type="edit" :href="route('customers.edit', $customer)" />
    
    ---
    
    OLD WAY:
    <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Are you sure?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-delete">
            <i class="btn-icon fa-solid fa-trash"></i>
            Delete
        </button>
    </form>
    
    NEW WAY:
    <x-action-button type="delete" :form-action="route('customers.destroy', $customer)" />
    
    ============================================================================
    COLOR REFERENCE
    ============================================================================
    
    Emerald (Create/Add): bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500
    Amber (Edit): bg-amber-600 hover:bg-amber-700 focus:ring-amber-500
    Rose/Red (Delete/Reject/Remove): bg-rose-600 hover:bg-rose-700 focus:ring-rose-500
    Sky (View): bg-sky-600 hover:bg-sky-700 focus:ring-sky-500
    Blue (Save/Submit/Upload): bg-blue-600 hover:bg-blue-700 focus:ring-blue-500
    Gray (Cancel/Back/Settings): bg-gray-600 hover:bg-gray-700 focus:ring-gray-500
    Purple (Print): bg-purple-600 hover:bg-purple-700 focus:ring-purple-500
    Teal (Export): bg-teal-600 hover:bg-teal-700 focus:ring-teal-500
    Indigo (Filter): bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500
    Green (Approve/Download): bg-green-600 hover:bg-green-700 focus:ring-green-500
    Cyan (Send): bg-cyan-600 hover:bg-cyan-700 focus:ring-cyan-500
    Violet (Share): bg-violet-600 hover:bg-violet-700 focus:ring-violet-500
    Slate (Copy): bg-slate-600 hover:bg-slate-700 focus:ring-slate-500
    Orange (Reset): bg-orange-600 hover:bg-orange-700 focus:ring-orange-500
    
--}}
