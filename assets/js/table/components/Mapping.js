import React from 'react'

import MappingImg from './MappingImg'

const Mapping = ({ type, start, stop, isoforms, mapping }) => {
    const widths = isoforms.reduce((widths, isoform) => {
        widths[isoform.accession] = isoform.is_canonical
            ? stop - start + 1
            : isoform.sequence.length
        return widths
    }, {})

    const maxwidth = Math.max(...Object.values(widths))

    const reduced = mapping.reduce((reduced, alignment) => {
        alignment.isoforms.map(isoform => {
            isoform.occurences.map(occurence => {
                if (! reduced[isoform.accession]) reduced[isoform.accession] = {
                    accession: isoform.accession,
                    start: 1,
                    stop: widths[isoform.accession],
                    width: widths[isoform.accession],
                    occurences: [],
                }

                reduced[isoform.accession].occurences.push(occurence)
            })
        })
        return reduced
    }, {})

    return (
        <React.Fragment>
            {Object.values(reduced).map((isoform, i) => (
                <div key={i} className="card">
                    <h5 className="card-header">
                        {isoform.accession}
                    </h5>
                    <div className="card-body">
                        <MappingImg
                            type={type}
                            start={isoform.start}
                            stop={isoform.stop}
                            width={maxwidth}
                        />
                        {isoform.occurences.map((occurence, j) => (
                            <MappingImg
                                key={j}
                                type={type}
                                start={occurence.start}
                                stop={occurence.stop}
                                width={maxwidth}
                            />
                        ))}
                    </div>
                </div>
            ))}
        </React.Fragment>
    )
}

export default Mapping
