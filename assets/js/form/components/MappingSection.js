import React from 'react'

import MappingModal from './MappingModal'
import MappingEditor from './MappingEditor'
import MappingDisplay from './MappingDisplay'

const MappingSection = ({ protein, fire, ...props }) => {
    const sequence = protein.sequence.slice(props.start - 1, props.stop)

    const sequences = protein.isoforms.reduce((sequences, isoform) => {
        sequences[isoform.accession] = isoform.is_canonical ? sequence : isoform.sequence
        return sequences
    }, {})

    const domains = protein.domains.map(domain => {
        return {
            key: domain.key,
            description: domain.description,
            start: domain.start - props.start + 1,
            stop: domain.stop - props.start + 1,
            valid: domain.start >= props.start && domain.stop <= props.stop,
        }
    })

    return (
        <React.Fragment>
            <MappingEditor {...props}
                sequence={sequence}
                domains={domains}
                fire={() => fire(props.query, sequences)}
            />
            <MappingDisplay {...props} sequences={sequences} />
            {! props.selecting ? null : (
                <MappingModal {...props} sequences={sequences} />
            )}
        </React.Fragment>
    )
}

export default MappingSection;
