import React from 'react'

import { MappingImg } from './MappingImg'

import { Protein, Isoform, Alignment } from '../types'

type Reduced = Record<string, Isoform>

type Coordinates = Record<string, { start: number, stop: number, width: number }>

type Props = {
    name: string,
    start: number,
    stop: number,
    protein: Protein,
    mapping: Alignment[],
}

export const Mapping: React.FC<Props> = ({ name, start, stop, protein, mapping }) => {
    const reduced: Reduced = mapping.reduce((reduced: Reduced, alignment: Alignment): Reduced => {
        alignment.isoforms.map((isoform: Isoform): void => {
            isoform.occurrences.map(occurrence => {
                if (!reduced[isoform.accession]) reduced[isoform.accession] = {
                    accession: isoform.accession,
                    occurrences: [],
                }

                reduced[isoform.accession].occurrences.push(occurrence)
            })
        })
        return reduced
    }
        , {})

    const coordinates: Coordinates = start == 1 && stop == protein.sequence.length
        ? protein.isoforms.reduce((reduced, isoform) => {
            reduced[isoform.accession] = {
                start: 1,
                stop: isoform.sequence.length,
                width: isoform.sequence.length,
            }
            return reduced
        }, {})
        : {
            [protein.accession]: {
                start: start,
                stop: stop,
                width: stop - start + 1
            }
        }

    return (
        <React.Fragment>
            {Object.values(reduced).map((isoform, i) => (
                <div key={i} className="card">
                    <h5 className="card-header">
                        {coordinates[isoform.accession].start == 1
                            ? isoform.accession
                            : [isoform.accession, '/', name].join('')} (
                            {coordinates[isoform.accession].start},&nbsp;
                            {coordinates[isoform.accession].stop}
                        )
                    </h5>
                    <div className="card-body">
                        {isoform.occurrences.sort((a, b) => a.start - b.start).map((occurrence, j) => (
                            <p key={j}>
                                <MappingImg
                                    type={protein.type}
                                    start={occurrence.start}
                                    stop={occurrence.stop}
                                    width={coordinates[isoform.accession].width}
                                />
                            </p>
                        ))}
                    </div>
                </div>
            ))}
        </React.Fragment>
    )
}
