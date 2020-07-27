import React from 'react'

export const SearchOverlay: React.FC = ({ children }) => (
    <div style={{ position: 'relative' }}>
        <div style={{ position: 'absolute', width: '100%', zIndex: 100 }}>
            {children}
        </div>
    </div>
)
