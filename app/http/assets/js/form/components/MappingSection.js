import React from 'react'

import MappingModal from './MappingModal'
import MappingEditor from './MappingEditor'
import MappingDisplay from './MappingDisplay'

const MappingSection = ({ protein, fire, ...props }) => {
    const isMature = props.start == 1 && props.stop == protein.sequence.length

    const sequence = protein.sequence.slice(props.start - 1, props.stop)

    const sequences = isMature
        ? protein.isoforms.reduce((sequences, isoform) => {
            sequences[isoform.accession] = isoform.sequence
            return sequences
        }, {})
        : {[protein.accession]: sequence}

    const coordinates = isMature
        ? protein.isoforms.reduce((reduced, isoform) => {
            reduced[isoform.accession] = {
                start: 1,
                stop: isoform.sequence.length,
                width: isoform.sequence.length,
            }
            return reduced
        }, {})
        : {[protein.accession]: {
            start: props.start,
            stop: props.stop,
            width: props.stop - props.start + 1
        }}

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
            <MappingDisplay {...props} coordinates={coordinates} />
            {! props.selecting ? null : (
                <MappingModal {...props} coordinates={coordinates} />
            )}
        </React.Fragment>
    )
}

export default MappingSection;
