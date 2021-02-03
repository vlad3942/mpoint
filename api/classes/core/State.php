<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sagar Narayane
 * Copyright: Cellpoint Mobile
 * Link: http://www.cellpointmobile.com
 * Project: mPoint PHP 7
 * Package: api\.classes.core
 * File Name:State.php
 */

class State
{
    private int $id;

    private string $name;

    /**
     * @var State[]
     */
    private array $sub_states = [];

    /**
     * State constructor.
     * @param int $id
     * @param string $name
     * @param array|null $sub_state
     */
    public function __construct(int $id, string $name, ?array $sub_states = null)
    {
        $this->id=$id;
        $this->name = $name;
        if($sub_states !== null)
        {
            $this->sub_states = $sub_states;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubStates(): array
    {
        return $this->sub_states;
    }

    public function addSubState(State $sub_state): void
    {
        array_push($this->sub_states, $sub_state);
    }

    public function addSubStates(array $sub_states): void
    {
        $this->sub_states = array_merge($this->sub_states,$sub_states);
    }

    public function asXML(): string
    {
        $xml = '<state><id>' . $this->getId() . '</id><name>' . $this->getName() . '</name>';
        if (count($this->sub_states) > 0) {
            $xml .= '<sub_states>';
            foreach ($this->sub_states as $sub_state) {
                $xml .= $sub_state->asXML();
            }
            $xml .= '</sub_states>';
        }
        return $xml . '</state>';
    }
}