import React from 'react'

import MappingImg from './MappingImg'

const Mapping = ({ type, start, stop, isoforms, mapping }) => {
    const widths = isoforms.reduce((widths, isoform) => {
        widths[isoform.accession] = isoform.is_canonical
            ? stop - start + 1
            : isoform.sequence.length
        return widths
    }, {})

    const reduced = mapping.reduce((reduced, alignment) => {
        alignment.isoforms.map(isoform => {
            isoform.occurrences.map(occurrence => {
                if (! reduced[isoform.accession]) reduced[isoform.accession] = {
                    accession: isoform.accession,
                    start: 1,
                    stop: widths[isoform.accession],
                    width: widths[isoform.accession],
                    occurrences: [],
                }

                reduced[isoform.accession].occurrences.push(occurrence)
            })
        })
        return reduced
    }, {})

    return (
        <React.Fragment>
            {Object.values(reduced).map((isoform, i) => (
                <div key={i} className="card">
                    <h5 className="card-header">
                        {isoform.accession} (length: {widths[isoform.accession]})
                    </h5>
                    <div className="card-body">
                        {isoform.occurrences.map((occurrence, j) => (
                            <p>
                                <MappingImg
                                    key={j}
                                    type={type}
                                    start={occurrence.start}
                                    stop={occurrence.stop}
                                    width={widths[isoform.accession]}
                                />
                            </p>
                        ))}
                    </div>
                </div>
            ))}
        </React.Fragment>
    )
}

export default Mapping
