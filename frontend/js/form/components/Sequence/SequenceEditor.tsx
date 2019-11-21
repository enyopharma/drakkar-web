import React, { useState } from 'react'

import { Mature, Chain } from '../../types'

import { ChainsFormGroup } from './ChainsFormGroup'
import { MatureProteinList } from './MatureProteinList'
import { CoordinateField } from '../Shared/CoordinateField'
import { ExtractFormGroup } from '../Shared/ExtractFormGroup'
import { SubsequenceFormGroup } from '../Shared/SubsequenceFormGroup'

type Props = {
    name: string,
    start: number,
    stop: number,
    sequence: string,
    matures: Mature[],
    chains: Chain[],
    update: (mature: Mature) => void,
}

export const SequenceEditor: React.FC<Props> = ({ name, start, stop, sequence, matures, chains, update }) => {
    const [mature, setMature] = useState<Mature>({ name: name, start: start, stop: stop })

    const isNameSet = mature.name.trim() != ''

    const isNameWellFormatted = mature.name.trim().match(/^[^\s]+$/)

    const areCoordinatesSet = mature.start != null && mature.stop != null

    const doesNameExist = matures.filter(m => {
        return m.name.trim().toLowerCase() == mature.name.trim().toLowerCase()
    }).length > 0

    const doCoordinatesExist = matures.filter(m => {
        return m.start == mature.start && m.stop == mature.stop
    }).length > 0

    const doesMatureExist = matures.filter(m => {
        return m.name.trim().toLowerCase() == mature.name.trim().toLowerCase()
            && m.start == mature.start
            && m.stop == mature.stop
    }).length > 0

    const isNameValid = !isNameSet || (isNameWellFormatted && !doesNameExist) || doesMatureExist

    const areCoordinatesValid = (!areCoordinatesSet || !doCoordinatesExist || doesMatureExist)
        && mature.start <= mature.stop

    const isMatureValid = isNameSet && isNameWellFormatted && areCoordinatesSet
        && (doesMatureExist || (!doesNameExist && !doCoordinatesExist))

    const setName = (name: string) => setMature({ name: name, start: mature.start, stop: mature.stop })
    const setStart = (start: number) => setMature({ name: mature.name, start: start, stop: mature.stop })
    const setStop = (stop: number) => setMature({ name: mature.name, start: mature.start, stop: stop })

    const setCoordinates = (start: number, stop: number) => setMature({
        name: mature.name,
        start: start,
        stop: stop,
    })

    const setFullLength = () => setCoordinates(1, sequence.length)

    return (
        <React.Fragment>
            {matures.length == 0
                ? <p>No sequence defined on this uniprot entry yet.</p>
                : (
                    <React.Fragment>
                        <p>
                            Existing sequences on this uniprot entry:
                        </p>
                        <MatureProteinList matures={matures} select={update} />
                    </React.Fragment>
                )
            }
            <div className="row">
                <div className="col-3">
                    <input
                        type="text"
                        className={'form-control' + (isNameValid ? '' : ' is-invalid')}
                        placeholder="Name"
                        value={mature.name}
                        onChange={e => setName(e.target.value)}
                    />
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={mature.start}
                        set={setStart}
                        max={sequence.length}
                        valid={areCoordinatesValid}
                        placeholder="Start"
                    />
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={mature.stop}
                        set={setStop}
                        max={sequence.length}
                        valid={areCoordinatesValid}
                        placeholder="Stop"
                    />
                </div>
                <div className="col-3">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={e => update(mature)}
                        disabled={!isMatureValid}
                    >
                        Validate
                    </button>
                </div>
            </div>
            {chains.length == 0 ? null : (
                <ChainsFormGroup chains={chains} set={setCoordinates}>
                    Extract coordinates
                </ChainsFormGroup>
            )}
            <SubsequenceFormGroup sequence={sequence} set={setCoordinates}>
                Extract coordinates
            </SubsequenceFormGroup>
            <ExtractFormGroup sequence={sequence} set={setCoordinates}>
                Extract coordinates
            </ExtractFormGroup>
            <div className="row">
                <div className="col offset-9">
                    <button
                        type="button"
                        className="btn btn-block btn-info"
                        onClick={e => setFullLength()}
                    >
                        Set to full length
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}
