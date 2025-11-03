const { registerBlockType } = wp.blocks;
const { useSelect } = wp.data;
const { __ } = wp.i18n;

// Register Film Details Block
registerBlockType('filmhanterare/film-details', {
    title: __('Film Details', 'filmhanterare'),
    icon: 'video-alt3',
    category: 'common',
    supports: {
        html: false,
        multiple: false
    },
    edit: function() {
        const postType = useSelect(select => 
            select('core/editor').getCurrentPostType()
        );

        if (postType !== 'film') {
            return (
                <div className="components-placeholder">
                    <div className="components-placeholder__label">
                        {__('Film Details', 'filmhanterare')}
                    </div>
                    <div className="components-placeholder__instructions">
                        {__('This block can only be used with film posts.', 'filmhanterare')}
                    </div>
                </div>
            );
        }

        return (
            <div className="film-details">
                <div className="components-placeholder">
                    <div className="components-placeholder__label">
                        {__('Film Details', 'filmhanterare')}
                    </div>
                    <div className="components-placeholder__instructions">
                        {__('Film details will be displayed here.', 'filmhanterare')}
                    </div>
                </div>
            </div>
        );
    },
    save: function() {
        return null; // Dynamic block, render callback will handle frontend
    }
});

// Register Showtimes Block
registerBlockType('filmhanterare/showtimes', {
    title: __('Film Showtimes', 'filmhanterare'),
    icon: 'calendar',
    category: 'common',
    supports: {
        html: false,
        multiple: false
    },
    edit: function() {
        const postType = useSelect(select => 
            select('core/editor').getCurrentPostType()
        );

        if (postType !== 'film') {
            return (
                <div className="components-placeholder">
                    <div className="components-placeholder__label">
                        {__('Film Showtimes', 'filmhanterare')}
                    </div>
                    <div className="components-placeholder__instructions">
                        {__('This block can only be used with film posts.', 'filmhanterare')}
                    </div>
                </div>
            );
        }

        return (
            <div className="film-showtimes">
                <div className="components-placeholder">
                    <div className="components-placeholder__label">
                        {__('Film Showtimes', 'filmhanterare')}
                    </div>
                    <div className="components-placeholder__instructions">
                        {__('Showtimes will be displayed here.', 'filmhanterare')}
                    </div>
                </div>
            </div>
        );
    },
    save: function() {
        return null; // Dynamic block, render callback will handle frontend
    }
});